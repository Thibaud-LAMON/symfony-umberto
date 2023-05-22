<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UniversesController extends AbstractController
{
    #[Route('/universes', name: 'app_universes')]
    public function index(): Response
    {
        return $this->render('universes/index.html.twig', [
            'controller_name' => 'UniversesController',
        ]);
    }
}
