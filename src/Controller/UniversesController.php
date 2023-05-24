<?php

namespace App\Controller;

use App\Entity\Universes;
use App\Entity\Projects;
use App\Entity\Users;
use App\Form\CreateUniverseType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectsRepository;
use App\Repository\SnippetsRepository;
use App\Repository\SuggestionsRepository;
use App\Repository\UniversesRepository;

class UniversesController extends AbstractController
{
    #[Route('/project/{projectId}/universes', name: 'app_universes')]
    public function index(ProjectsRepository $projectsRepository, SnippetsRepository $snippetsRepository, SuggestionsRepository $suggestionsRepository, UniversesRepository $universesRepository, Request $request, EntityManagerInterface $entityManager, int $projectId): Response
    {
        /** @var Users $user */
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $userId = $user->getId(); // Récupère l'ID de l'utilisateur connecté
        $countProjects = $projectsRepository->countByUserId($userId); // Récupère le nombre d'entrées
        $countSnippets = $snippetsRepository->countBySnippet($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countTrunc = $snippetsRepository->countByTrunc($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countSuggestions = $suggestionsRepository->countBySuggestion($userId); // Récupère le nombre de Snippets créés par cet utilisateur

        $project = $entityManager->getRepository(Projects::class)->find($projectId);

        if (!$project) {
            throw new EntityNotFoundException('Project with ID "' . $projectId . '" does not exist.');
        }

        $displayUniv = $universesRepository->findByProjectId($projectId);

        $universe = new Universes();
        $universe->setProjects($project);
        $form = $this->createForm(CreateUniverseType::class, $universe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($universe);
            $entityManager->flush();

            $this->addFlash('success', 'L\'univers a été créé avec succès.');

            return $this->redirectToRoute('app_universes');
        }

        return $this->render('universes/index.html.twig', [
            'controller_name' => 'UniversesController',
            'projects' => $countProjects,
            'snippets' => $countSnippets,
            'truncated' => $countTrunc,
            'suggestions' => $countSuggestions,
            'univTab' => $displayUniv,
            'universeForm' => $form->createView(),
        ]);
    }
}
