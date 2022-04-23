<?php

namespace SimplyStream\SoulsDeathBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use SimplyStream\SoulsDeathBundle\Entity\Section;
use SimplyStream\SoulsDeathBundle\Entity\Tracker;
use SimplyStream\SoulsDeathBundle\Form\TrackerType;
use SimplyStream\SoulsDeathBundle\Service\TrackerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TrackerController extends AbstractController
{
    protected FormFactoryInterface $formFactory;

    protected EntityManagerInterface $entityManager;

    protected TrackerService $trackerService;

    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        TrackerService $trackerService
    ) {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->trackerService = $trackerService;
    }

    public function index(): Response
    {
        $form = $this->formFactory->create(TrackerType::class);

        return $this->render('@SimplyStreamSoulsDeath/tracker/index.html.twig', [
            'trackers' => $this->trackerService->getByOwner($this->getUser()),
            'form' => $form->createView(),
        ]);
    }

    public function save(Request $request): Response
    {
        $form = $this->formFactory->create(TrackerType::class, null, [
            'csrf_protection' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Tracker $data */
            $data = $form->getData();
            $data->setOwner($this->getUser());

            foreach ($data->getSections() as $section) {
                $this->entityManager->persist($section);

                foreach ($section->getDeaths() as $death) {
                    $this->entityManager->persist($death);
                }
            }

            $this->entityManager->persist($data);
            $this->entityManager->flush();

            return $this->redirectToRoute('simplystream.get_tracker', ['id' => $data->getId()]);
        }

        return $this->redirectToRoute('simplystream.get_trackers', [], Response::HTTP_BAD_REQUEST);
    }

    public function getTracker(string $id): Response
    {
        $tracker = $this->trackerService->get($id);

        if (! $tracker) {
            throw $this->createNotFoundException("Tracker with ID '${id}' not found");
        }

        return $this->render('@SimplyStreamSoulsDeath/tracker/get.html.twig', [
            'tracker' => $tracker,
            'total' => $this->trackerService->getTotal($tracker),
        ]);
    }

    public function getTrackerOverlayTotal(string $id, Request $request): Response
    {
        $tracker = $this->trackerService->get($id);

        if (! $tracker) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            '@SimplyStreamSoulsDeath/tracker/overlay/total.html.twig', [
            'trackerId' => $tracker->getId(),
            'total' => $this->trackerService->getTotal($tracker),
            'twitchId' => $request->query->get('twitchId'),
        ]);
    }

    public function getTrackerOverlay(string $id, Request $request): Response
    {
        $tracker = $this->trackerService->get($id);

        if (! $tracker) {
            throw $this->createNotFoundException();
        }

        return $this->render('@SimplyStreamSoulsDeath/tracker/overlay/list.html.twig', [
            'tracker' => $tracker,
            'total' => $this->trackerService->getTotal($tracker),
            'twitchId' => $request->query->get('twitchId'),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function getSharableUrl(string $id): Response
    {
        $tracker = $this->trackerService->get($id);

        if (! $tracker) {
            throw $this->createNotFoundException("Tracker with ID '${id}' not found");
        }

        if (! $tracker->getPublicToken()) {
            $tracker->setPublicToken($this->trackerService->generatePublicToken());
            $this->trackerService->save($tracker, true);
        }

        return $this->json([
            'publicUrl' => $this->generateUrl(
                'simplystream.get_tracker',
                [
                    'id' => $id,
                    'token' => $tracker->getPublicToken(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ]);
    }

    // @TODO: This is hella inefficient and needs to be re-done!
    //        But for now it's ok
    public function editTracker(string $id, Request $request): Response
    {
        if (null === $tracker = $this->trackerService->get($id)) {
            throw $this->createNotFoundException('No tracker found for id ' . $id);
        }

        $form = $this->formFactory->create(TrackerType::class, $tracker, [
            'csrf_protection' => false,
        ]);

        $originalSections = new ArrayCollection($tracker->getSections()->toArray());
        $originalDeaths = new ArrayCollection();

        /** @var Section $section */
        foreach ($originalSections as $section) {
            foreach ($section->getDeaths() as $death) {
                $originalDeaths->add($death);
            }
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Tracker $newTracker */
            $newTracker = $form->getData();

            // Check if section needs to be added
            foreach ($newTracker->getSections() as $section) {
                // @TODO: Fix by adding hidden tracker field to form
                if (false === $originalSections->contains($section)) {
                    $section->setTracker($tracker);
                }

                // Check if new death has been added
                // @TODO: Fix by adding hidden section field to form
                foreach ($section->getDeaths() as $death) {
                    if (false === $originalDeaths->contains($death)) {
                        $this->entityManager->persist($death);
                        $death->setSection($section);
                    }
                }

                foreach ($originalDeaths as $death) {
                    if ($death->getSection() === $section &&
                        false === $section->getDeaths()->contains($death)) {
                        $this->entityManager->remove($death);
                    }
                }
            }

            // Check if section needs to be removed
            foreach ($originalSections as $section) {
                if (false === $newTracker->getSections()->contains($section)) {
                    $this->entityManager->remove($section);
                }
            }

            $this->entityManager->persist($newTracker);
            $this->entityManager->flush();

            return $this->redirectToRoute('simplystream.get_tracker', ['id' => $newTracker->getId()]);
        }

        return $this->render('@SimplyStreamSoulsDeath/tracker/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
