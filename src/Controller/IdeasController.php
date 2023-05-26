<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IdeasController extends AbstractController
{
    #[Route('/ideas', name: 'app_ideas')]
    public function index(): Response
    {
        return $this->render('ideas/index.html.twig', [
            'controller_name' => 'IdeasController',
        ]);
    }
}
