<?php

namespace SimplyStream\SoulsDeathBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use SimplyStream\SoulsDeathBundle\Entity\Section;
use SimplyStream\SoulsDeathBundle\Entity\Tracker;
use SimplyStream\SoulsDeathBundle\Form\TrackerType;
use SimplyStream\SoulsDeathBundle\Repository\TrackerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackerController extends AbstractController
{
    protected TrackerRepository $trackerRepository;
    protected FormFactoryInterface $formFactory;
    protected EntityManagerInterface $entityManager;

    public function __construct(
        TrackerRepository $trackerRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->trackerRepository = $trackerRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
    }

    public function index(): Response
    {
        $form = $this->formFactory->create(TrackerType::class);

        return $this->render('@SimplyStreamSoulsDeath/tracker/index.html.twig', [
            'trackers' => $this->trackerRepository->findBy(['owner' => $this->getUser()]),
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

            return $this->redirect('/tracker/' . $data->getId());
        }

        return $this->redirect('/tracker', Response::HTTP_BAD_REQUEST);
    }

    public function getTracker(string $id): Response
    {
        $tracker = $this->trackerRepository->find($id);
        $total = 0;

        if ($tracker) {
            foreach ($tracker->getSections() as $section) {
                $total += $section->getTotalDeaths();
            }
        }

        return $this->render('@SimplyStreamSoulsDeath/tracker/get.html.twig', [
            'tracker' => $tracker,
            'total' => $total,
        ]);
    }

    public function getTrackerOverlayTotal(string $id): Response
    {
        $tracker = $this->trackerRepository->find($id);
        $total = 0;

        if (! $tracker) {
            $this->createNotFoundException();
        }

        foreach ($tracker->getSections() as $section) {
            foreach ($section->getDeaths() as $death) {
                $total += $death->getDeaths();
            }
        }

        return $this->render(
            '@SimplyStreamSoulsDeath/tracker/total.html.twig', [
            'total' => $total,
            'trackerId' => $tracker->getId(),
        ]);
    }

    // @TODO: This is hella inefficient and needs to be re-done!
    //        But for now it's ok
    public function editTracker(string $id, Request $request): Response
    {
        if (null === $tracker = $this->trackerRepository->find($id)) {
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

            return $this->redirect('/tracker/' . $newTracker->getId());
        }

        return $this->render('@SimplyStreamSoulsDeath/tracker/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
