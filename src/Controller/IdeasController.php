<?php

namespace App\Controller;

use App\Entity\Ideas;
use App\Entity\Branches;
use App\Entity\Users;
use App\Form\CreateIdeaType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectsRepository;
use App\Repository\SnippetsRepository;
use App\Repository\SuggestionsRepository;
use App\Repository\IdeasRepository;

class IdeasController extends AbstractController
{
    #[Route('project/{projectId}/universes/{universeId}/branches/{branchId}/ideas', name: 'app_ideas')]
    public function index(ProjectsRepository $projectsRepository, SnippetsRepository $snippetsRepository, SuggestionsRepository $suggestionsRepository, IdeasRepository $ideasRepository, Request $request, EntityManagerInterface $entityManager, int $projectId, int $universeId, int $branchId): Response
    {
        /** @var Users $user */
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $userId = $user->getId(); // Récupère l'ID de l'utilisateur connecté
        $countProjects = $projectsRepository->countByUserId($userId); // Récupère le nombre d'entrées
        $countSnippets = $snippetsRepository->countBySnippet($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countTrunc = $snippetsRepository->countByTrunc($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countSuggestions = $suggestionsRepository->countBySuggestion($userId); // Récupère le nombre de Snippets créés par cet utilisateur

        $branch = $entityManager->getRepository(Branches::class)->find($branchId);

        if (!$branch) {
            throw new EntityNotFoundException('Branch with ID "' . $branchId . '" does not exist.');
        }

        $displayIdeas = $ideasRepository->findByBranchId($branchId);

        $idea = new Ideas();
        $idea->setBranches($branch);
        $form = $this->createForm(CreateIdeaType::class, $idea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($idea);
            $entityManager->flush();

            $this->addFlash('success', 'L\'idée a été créée avec succès.');

            return $this->redirectToRoute('app_ideas', ['projectId' => $projectId, 'universeId' => $universeId, 'branchId' => $branchId]);
        }
        return $this->render('ideas/index.html.twig', [
            'controller_name' => 'IdeasController',
            'projects' => $countProjects,
            'snippets' => $countSnippets,
            'truncated' => $countTrunc,
            'suggestions' => $countSuggestions,
            'ideaTab' => $displayIdeas,
            'ideaForm' => $form->createView(),
            'projectId' => $projectId, // Passe projectId à la vue
            'universeId' => $universeId, // Passe universeId à la vue
            'branchId' => $branchId, // Passe branchId à la vue
        ]);
    }
}
