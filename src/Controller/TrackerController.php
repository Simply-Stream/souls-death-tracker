<?php

namespace App\Controller;

use App\Repository\TrackerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrackerController extends AbstractController
{
    #[Route('/tracker', name: 'tracker')]
    public function index(TrackerRepository $trackerRepository): Response
    {
        return $this->render('tracker/index.html.twig', [
            'trackers' => $trackerRepository->findBy(['owner' => $this->getUser()]),
        ]);
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
}
