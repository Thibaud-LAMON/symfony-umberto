<?php

namespace App\Controller;

use App\Entity\Branches;
use App\Entity\Universes;
use App\Entity\Users;
use App\Form\CreateBrancheType;
use App\Entity\Projects;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectsRepository;
use App\Repository\SnippetsRepository;
use App\Repository\SuggestionsRepository;
use App\Repository\BranchesRepository;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BranchesController extends AbstractController
{
    #[Route('/project/{projectId}/universes/{universeId}/branches', name: 'app_branches')]
    public function index(ProjectsRepository $projectsRepository, SnippetsRepository $snippetsRepository, SuggestionsRepository $suggestionsRepository, BranchesRepository $branchesRepository, Request $request, EntityManagerInterface $entityManager, Breadcrumbs $breadcrumbs, int $projectId, int $universeId): Response
    {
        /** @var Users $user */
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $userId = $user->getId(); // Récupère l'ID de l'utilisateur connecté
        $countProjects = $projectsRepository->countByUserId($userId); // Récupère le nombre d'entrées
        $countSnippets = $snippetsRepository->countBySnippet($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countTrunc = $snippetsRepository->countByTrunc($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countSuggestions = $suggestionsRepository->countBySuggestion($userId); // Récupère le nombre de Snippets créés par cet utilisateur

        $project = $entityManager->getRepository(Projects::class)->find($projectId);
        $universe = $entityManager->getRepository(Universes::class)->find($universeId);

        if (!$project) {
            throw new EntityNotFoundException('Project with ID "' . $projectId . '" does not exist.');
        }

        if (!$universe) {
            throw new EntityNotFoundException('Universe with ID "' . $universeId . '" does not exist.');
        }

        // Ajout des éléments de fil d'Ariane
        $breadcrumbs->addItem($project->getName(), $this->generateUrl('app_projects_main', ['projectId' => $projectId]));
        $breadcrumbs->addItem($universe->getName());

        $displayBranches = $branchesRepository->findByUniverseId($universeId);

        $branch = new Branches();
        $branch->setUniverses($universe);
        $form = $this->createForm(CreateBrancheType::class, $branch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($branch);
            $entityManager->flush();

            $this->addFlash('success', 'La branche a été créée avec succès.');

            return $this->redirectToRoute('app_branches', ['projectId' => $projectId, 'universeId' => $universeId]);
        }

        return $this->render('branches/index.html.twig', [
            'controller_name' => 'BranchesController',
            'projects' => $countProjects,
            'snippets' => $countSnippets,
            'truncated' => $countTrunc,
            'suggestions' => $countSuggestions,
            'branchTab' => $displayBranches,
            'branchForm' => $form->createView(),
            'projectId' => $projectId, // Passe projectId à la vue
            'universeId' => $universeId, // Passe universeId à la vue
        ]);
    }
}
