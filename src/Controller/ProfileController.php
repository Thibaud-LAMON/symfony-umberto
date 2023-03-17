<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profil', name: 'app_profile_')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Security $security): Response
    {
        $user = $security->getUser();
        $username = $user;

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'Profil de l\'utilisateur',
            'username' => $username,
        ]);
    }
}
