<?php

namespace App\Controller;

use App\Entity\Tracker;
use App\Form\TrackerType;
use App\Repository\TrackerRepository;
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
}
