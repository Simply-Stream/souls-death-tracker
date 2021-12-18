<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrackerController extends AbstractController
{
    #[Route('/tracker', name: 'tracker')]
    public function index(): Response
    {
        return $this->render('tracker/index.html.twig', [
            'controller_name' => 'TrackerController',
        ]);
    }
}
