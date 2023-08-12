<?php

namespace App\Controller;

use App\Entity\Branches;
use App\Entity\Universes;
use App\Entity\Projects;
use App\Entity\Users;
use App\Entity\Ideas;
use App\Form\TruncationType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectsRepository;
use App\Repository\SnippetsRepository;
use App\Repository\SuggestionsRepository;
use App\Repository\UniversesRepository;
use App\Repository\BranchesRepository;
use App\Repository\IdeasRepository;
use App\Repository\SynonymsRepository;
use Symfony\Component\HttpFoundation\Request;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class TreatmentController extends AbstractController
{
    #[Route('/treatment/projects', name: 'app_treatment_first')]
    public function project_select(ProjectsRepository $projectsRepository, SnippetsRepository $snippetsRepository, Breadcrumbs $breadcrumbs, SuggestionsRepository $suggestionsRepository): Response
    {
        /** @var Users $user */
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $userId = $user->getId(); // Récupère l'ID de l'utilisateur connecté
        $countProjects = $projectsRepository->countByUserId($userId); // Récupère le nombre d'entrées
        $countSnippets = $snippetsRepository->countBySnippet($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countTrunc = $snippetsRepository->countByTrunc($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countSuggestions = $suggestionsRepository->countBySuggestion($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $userProjects = $projectsRepository->findByUserId($userId); // Récupère les projets de l'utilisateur

        $breadcrumbs->addItem("Traitement");

        return $this->render('treatment/project.html.twig', [
            'controller_name' => 'TreatmentController',
            'projects' => $countProjects,
            'snippets' => $countSnippets,
            'truncated' => $countTrunc,
            'suggestions' => $countSuggestions,
            'userProjects' => $userProjects,
        ]);
    }

    #[Route('/treatment/project/{projectId}/universes', name: 'app_treatment_second')]
    public function universes_select(ProjectsRepository $projectsRepository, SnippetsRepository $snippetsRepository, SuggestionsRepository $suggestionsRepository, UniversesRepository $universesRepository, EntityManagerInterface $entityManager, Breadcrumbs $breadcrumbs, int $projectId): Response
    {
        /** @var Users $user */
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $userId = $user->getId(); // Récupère l'ID de l'utilisateur connecté
        $countProjects = $projectsRepository->countByUserId($userId); // Récupère le nombre d'entrées
        $countSnippets = $snippetsRepository->countBySnippet($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countTrunc = $snippetsRepository->countByTrunc($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countSuggestions = $suggestionsRepository->countBySuggestion($userId); // Récupère le nombre de Snippets créés par cet utilisateur

        $project = $entityManager->getRepository(Projects::class)->findOneBy([
            'id' => $projectId,
            'users' => $user
        ]);

        if (!$project) {
            throw new EntityNotFoundException('Accès interdit ou projet non trouvé.');
        }

        $breadcrumbs->addItem("Traitement", $this->generateUrl('app_treatment_first', ['projectId' => $projectId]));
        $breadcrumbs->addItem($project->getName());

        $displayUniv = $universesRepository->findByProjectId($projectId);

        return $this->render('treatment/univ.html.twig', [
            'controller_name' => 'TreatmentController',
            'projects' => $countProjects,
            'snippets' => $countSnippets,
            'truncated' => $countTrunc,
            'suggestions' => $countSuggestions,
            'univTab' => $displayUniv,
            'projectId' => $projectId,
        ]);
    }

    #[Route('/treatment/project/{projectId}/universe/{universeId}/branches', name: 'app_treatment_third')]
    public function branches_select(ProjectsRepository $projectsRepository, SnippetsRepository $snippetsRepository, SuggestionsRepository $suggestionsRepository, BranchesRepository $branchesRepository, EntityManagerInterface $entityManager, Breadcrumbs $breadcrumbs, int $projectId, int $universeId): Response
    {
        /** @var Users $user */
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $userId = $user->getId(); // Récupère l'ID de l'utilisateur connecté
        $countProjects = $projectsRepository->countByUserId($userId); // Récupère le nombre d'entrées
        $countSnippets = $snippetsRepository->countBySnippet($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countTrunc = $snippetsRepository->countByTrunc($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countSuggestions = $suggestionsRepository->countBySuggestion($userId); // Récupère le nombre de Snippets créés par cet utilisateur

        $project = $entityManager->getRepository(Projects::class)->findOneBy([
            'id' => $projectId,
            'users' => $user
        ]);
        $universe = $entityManager->getRepository(Universes::class)->findOneBy([
            'id' => $universeId,
            'projects' => $project
        ]);

        if (!$project) {
            throw new EntityNotFoundException('Accès interdit ou projet non trouvé.');
        }
        if (!$universe) {
            throw new EntityNotFoundException('Accès interdit ou univers non trouvé.');
        }

        $breadcrumbs->addItem("Traitement", $this->generateUrl('app_treatment_first', ['projectId' => $projectId]));
        $breadcrumbs->addItem($project->getName(), $this->generateUrl('app_treatment_second', ['projectId' => $projectId, 'universeId' => $universeId]));
        $breadcrumbs->addItem($universe->getName());

        $displayBranches = $branchesRepository->findByUniverseId($universeId);

        return $this->render('treatment/branch.html.twig', [
            'controller_name' => 'TreatmentController',
            'projects' => $countProjects,
            'snippets' => $countSnippets,
            'truncated' => $countTrunc,
            'suggestions' => $countSuggestions,
            'branchTab' => $displayBranches,
            'projectId' => $projectId,
            'universeId' => $universeId,
        ]);
    }

    #[Route('/treatment/project/{projectId}/universe/{universeId}/branches/{branchId}/ideas', name: 'app_treatment_fourth')]
    public function ideas_select(ProjectsRepository $projectsRepository, SnippetsRepository $snippetsRepository, SuggestionsRepository $suggestionsRepository, IdeasRepository $ideasRepository, EntityManagerInterface $entityManager, Breadcrumbs $breadcrumbs, int $projectId, int $universeId, int $branchId): Response
    {
        /** @var Users $user */
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $userId = $user->getId(); // Récupère l'ID de l'utilisateur connecté
        $countProjects = $projectsRepository->countByUserId($userId); // Récupère le nombre d'entrées
        $countSnippets = $snippetsRepository->countBySnippet($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countTrunc = $snippetsRepository->countByTrunc($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countSuggestions = $suggestionsRepository->countBySuggestion($userId); // Récupère le nombre de Snippets créés par cet utilisateur

        $project = $entityManager->getRepository(Projects::class)->findOneBy([
            'id' => $projectId,
            'users' => $user
        ]);
        $universe = $entityManager->getRepository(Universes::class)->findOneBy([
            'id' => $universeId,
            'projects' => $project
        ]);
        $branch = $entityManager->getRepository(Branches::class)->findOneBy([
            'id' => $branchId,
            'universes' => $universe
        ]);

        if (!$project) {
            throw new EntityNotFoundException('Accès interdit ou projet non trouvé.');
        }
        if (!$universe) {
            throw new EntityNotFoundException('Accès interdit ou univers non trouvé.');
        }
        if (!$branch) {
            throw new EntityNotFoundException('Accès interdit ou branche non trouvée.');
        }

        $breadcrumbs->addItem("Traitement", $this->generateUrl('app_treatment_first', ['projectId' => $projectId]));
        $breadcrumbs->addItem($project->getName(), $this->generateUrl('app_treatment_second', ['projectId' => $projectId, 'universeId' => $universeId]));
        $breadcrumbs->addItem($universe->getName(), $this->generateUrl('app_treatment_third', ['projectId' => $projectId, 'universeId' => $universeId, 'branchId' => $branchId]));
        $breadcrumbs->addItem($branch->getName());

        $displayIdeas = $ideasRepository->findByBranchId($branchId);

        return $this->render('treatment/idea.html.twig', [
            'controller_name' => 'TreatmentController',
            'projects' => $countProjects,
            'snippets' => $countSnippets,
            'truncated' => $countTrunc,
            'suggestions' => $countSuggestions,
            'ideaTab' => $displayIdeas,
            'projectId' => $projectId,
            'universeId' => $universeId,
            'branchId' => $branchId,
        ]);
    }

    #[Route('/treatment/project/{projectId}/universe/{universeId}/branches/{branchId}/ideas/{ideaId}', name: 'app_treatment_fifth')]
    public function treatment(ProjectsRepository $projectsRepository, SnippetsRepository $snippetsRepository, SuggestionsRepository $suggestionsRepository, Request $request, EntityManagerInterface $entityManager, Breadcrumbs $breadcrumbs, int $projectId, int $universeId, int $branchId, int $ideaId): Response
    {
        /** @var Users $user */
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $userId = $user->getId(); // Récupère l'ID de l'utilisateur connecté
        $countProjects = $projectsRepository->countByUserId($userId); // Récupère le nombre d'entrées
        $countSnippets = $snippetsRepository->countBySnippet($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countTrunc = $snippetsRepository->countByTrunc($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countSuggestions = $suggestionsRepository->countBySuggestion($userId); // Récupère le nombre de Snippets créés par cet utilisateur

        $project = $entityManager->getRepository(Projects::class)->findOneBy([
            'id' => $projectId,
            'users' => $user
        ]);
        $universe = $entityManager->getRepository(Universes::class)->findOneBy([
            'id' => $universeId,
            'projects' => $project
        ]);
        $branch = $entityManager->getRepository(Branches::class)->findOneBy([
            'id' => $branchId,
            'universes' => $universe
        ]);
        $idea = $entityManager->getRepository(Ideas::class)->findOneBy([
            'id' => $ideaId,
            'branches' => $branch
        ]);

        if (!$project) {
            throw new EntityNotFoundException('Accès interdit ou projet non trouvé.');
        }
        if (!$universe) {
            throw new EntityNotFoundException('Accès interdit ou univers non trouvé.');
        }
        if (!$branch) {
            throw new EntityNotFoundException('Accès interdit ou branche non trouvée.');
        }
        if (!$idea) {
            throw new EntityNotFoundException('Accès interdit ou idée non trouvée.');
        }

        $breadcrumbs->addItem("Traitement", $this->generateUrl('app_treatment_first', ['projectId' => $projectId]));
        $breadcrumbs->addItem($project->getName(), $this->generateUrl('app_treatment_second', ['projectId' => $projectId, 'universeId' => $universeId]));
        $breadcrumbs->addItem($universe->getName(), $this->generateUrl('app_treatment_third', ['projectId' => $projectId, 'universeId' => $universeId, 'branchId' => $branchId]));
        $breadcrumbs->addItem($branch->getName(), $this->generateUrl('app_treatment_fourth', ['projectId' => $projectId, 'universeId' => $universeId, 'branchId' => $branchId, 'ideaId' => $ideaId]));
        $breadcrumbs->addItem($idea->getName());

        $snippetEntity = $snippetsRepository->findOneBy(['ideas' => $ideaId, 'truncated' => null]);
        if (!$snippetEntity) {
            $this->addFlash('error', 'Tous les snippets sont traités.');
            return $this->redirectToRoute('app_treatment_final', [
                'projectId' => $projectId,
                'universeId' => $universeId,
                'branchId' => $branchId,
                'ideaId' => $ideaId
            ]); // Route to redirect to when all snippets are processed
        }

        $letters = str_split($snippetEntity->getSnippet());
        $subStrings = [];
        foreach ($letters as $index => $letter) {
            $subStrings[] = substr($snippetEntity->getSnippet(), 0, $index + 1);
        }

        $snippetEntity->setTruncated(end($subStrings));

        $form = $this->createForm(TruncationType::class, $snippetEntity);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($snippetEntity);
            $entityManager->flush();

            $this->addFlash('success', 'La modification a été enregistrée avec succès.');
            return $this->redirectToRoute('app_treatment_fifth', [
                'projectId' => $projectId,
                'universeId' => $universeId,
                'branchId' => $branchId,
                'ideaId' => $ideaId
            ]); // Redirect to the same page after saving the changes
        }

        return $this->render('treatment/truncating.html.twig', [
            'controller_name' => 'TreatmentController',
            'projects' => $countProjects,
            'snippets' => $countSnippets,
            'truncated' => $countTrunc,
            'suggestions' => $countSuggestions,
            'form' => $form->createView(),
            'snippet' => $snippetEntity->getSnippet(),
            'snippetId' => $snippetEntity->getId(),
            'letters' => $letters,
            'subStrings' => $subStrings,
            'projectId' => $projectId,
            'universeId' => $universeId,
            'branchId' => $branchId,
            'ideaId' => $ideaId,
        ]);
    }

    #[Route('/treatment/project/{projectId}/universe/{universeId}/branches/{branchId}/ideas/{ideaId}/table', name: 'app_treatment_final')]
    public function tableau(ProjectsRepository $projectsRepository, SnippetsRepository $snippetsRepository, SuggestionsRepository $suggestionsRepository, IdeasRepository $ideasRepository, SynonymsRepository $synonymsRepository, int $projectId, int $universeId, int $branchId, int $ideaId): Response
    {
        /** @var Users $user */
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $userId = $user->getId(); // Récupère l'ID de l'utilisateur connecté
        $countProjects = $projectsRepository->countByUserId($userId); // Récupère le nombre d'entrées
        $countSnippets = $snippetsRepository->countBySnippet($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countTrunc = $snippetsRepository->countByTrunc($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countSuggestions = $suggestionsRepository->countBySuggestion($userId); // Récupère le nombre de Snippets créés par cet utilisateur

        $ideaEntity = $ideasRepository->findIdeaById($ideaId);

        $truncatedSnippets = $snippetsRepository->findAllTruncatedByIdea($ideaId);
        $suggestions = $suggestionsRepository->findAllByIdea($ideaId);
        $synonyms = $synonymsRepository->findAllByIdea($ideaId);

        $results = [];

        $max_length = max(count($truncatedSnippets), count($suggestions), count($synonyms));

        for ($i = 0; $i < $max_length; $i++) {
            $truncSnippet = $i < count($truncatedSnippets) ? $truncatedSnippets[$i]->getTruncated() : Null; // valeur par défaut
            $suggestion = $i < count($suggestions) ? $suggestions[$i]->getSuggestion() : Null; // valeur par défaut
            $synonym = $i < count($synonyms) ? $synonyms[$i]->getSynonym() : Null; // valeur par défaut
            $results[] = ['truncatedSnippets' => $truncSnippet, 'suggestion' => $suggestion, 'synonym' => $synonym];
        }

        return $this->render('treatment/table.html.twig', [
            'controller_name' => 'TreatmentController',
            'projects' => $countProjects,
            'snippets' => $countSnippets,
            'truncated' => $countTrunc,
            'suggestions' => $countSuggestions,
            'projectId' => $projectId,
            'universeId' => $universeId,
            'branchId' => $branchId,
            'ideaId' => $ideaId,
            'ideaName' => $ideaEntity,
            'results' => $results
        ]);
    }
}
