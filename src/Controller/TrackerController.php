<?php

namespace App\Controller;

use App\Entity\Section;
use App\Entity\Tracker;
use App\Form\TrackerType;
use App\Repository\TrackerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrackerController extends AbstractController
{
    #[Route('/tracker', name: 'tracker', methods: ['GET'])]
    public function index(TrackerRepository $trackerRepository, FormFactoryInterface $formFactory): Response
    {
        $form = $formFactory->create(TrackerType::class);

        return $this->render('tracker/index.html.twig', [
            'trackers' => $trackerRepository->findBy(['owner' => $this->getUser()]),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tracker', name: 'tracker_save', methods: ['POST'])]
    public function save(Request $request, EntityManagerInterface $entityManager, FormFactoryInterface $formFactory): Response
    {
        $form = $formFactory->create(TrackerType::class, null, [
            'csrf_protection' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Tracker $data */
            $data = $form->getData();
            $data->setOwner($this->getUser());

            foreach ($data->getSections() as $section) {
                $entityManager->persist($section);

                foreach ($section->getDeaths() as $death) {
                    $entityManager->persist($death);
                }
            }

            $entityManager->persist($data);
            $entityManager->flush();

            return $this->redirect('/tracker/' . $data->getId());
        }

        return $this->redirect('/tracker', Response::HTTP_BAD_REQUEST);
    }

    #[Route('/tracker/{id}', name: 'get_tracker')]
    public function getTracker(string $id, TrackerRepository $trackerRepository): Response
    {
        $tracker = $trackerRepository->find($id);
        $total = 0;

        if ($tracker) {
            foreach ($tracker->getSections() as $section) {
                $total += $section->getTotalDeaths();
            }
        }

        return $this->render('tracker/get.html.twig', [
            'tracker' => $tracker,
            'total' => $total,
        ]);
    }

    #[Route('/tracker/{id}/overlay/total', name: 'get_tracker_overlay_total')]
    public function getTrackerOverlayTotal(string $id, TrackerRepository $trackerRepository): Response
    {
        $tracker = $trackerRepository->find($id);
        $total = 0;

        foreach ($tracker->getSections() as $section) {
            foreach ($section->getDeaths() as $death) {
                $total += $death->getDeaths();
            }
        }

        return $this->render('tracker/overlay-total.html.twig', [
            'total' => $total,
        ]);
    }

    // @TODO: This is hella inefficient and needs to be re-done!
    //        But for now it's ok
    #[Route('/tracker/{id}/edit', name: 'edit_tracker')]
    public function editTracker(
        string $id,
        Request $request,
        TrackerRepository $trackerRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager
    ): Response {
        if (null === $tracker = $trackerRepository->find($id)) {
            throw $this->createNotFoundException('No tracker found for id ' . $id);
        }

        $form = $formFactory->create(TrackerType::class, $tracker, [
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
                if (false === $originalSections->contains($section)) {
                    $section->setTracker($tracker);
                }

                foreach ($originalDeaths as $death) {
                    if ($death->getSection() === $section &&
                        false === $section->getDeaths()->contains($death)) {
                        $entityManager->remove($death);
                    }
                }
            }

            // Check if section needs to be removed
            foreach ($originalSections as $section) {
                if (false === $newTracker->getSections()->contains($section)) {
                    $entityManager->remove($section);
                }
            }

            $entityManager->persist($newTracker);
            $entityManager->flush();

            return $this->redirect('/tracker/' . $newTracker->getId());
        }

        return $this->render('tracker/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
