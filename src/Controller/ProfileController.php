<?php

namespace App\Controller;

//use App\Entity\Users;
use App\Form\EditProfileType;
use App\Form\EditPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
//use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route('/profil/edit', name: 'app_profil_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Profil mis à jour');
        }
        return $this->render('profile/edit.html.twig', [
            'formEdit' => $form->createView(),
        ]);
    }

    #[Route('/profil/password', name: 'app_profil_password')]
    public function password(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Le mot de passe a été modifié avec succès.');
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('echec', 'Il y a eu un problème, veuillez réessayer.');
        }
        return $this->render('profile/password.html.twig', [
            'formPassword' => $form->createView(),
        ]);
    }
}
