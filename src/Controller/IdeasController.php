<?php

namespace App\Controller;

use App\Entity\Snippets;
use App\Entity\Suggestions;
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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


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

    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    private function callScaleSerpAPISnippets($query): array
    {
        $apiKey = $this->params->get('SCALE_SERP_API_KEY');

        $queryString = http_build_query([
            'api_key' => $apiKey,
            'q' => $query,
            'output' => 'csv',
            'google_domain' => 'google.fr',
            'gl' => 'fr',
            'hl' => 'fr',
            'device' => 'desktop',
            'num' => '15',
            'csv_fields' => 'organic_results.snippet'
        ]);

        $ch = curl_init(sprintf('%s?%s', 'https://api.scaleserp.com/search', $queryString));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);

        $api_result = curl_exec($ch);
        curl_close($ch);

        $output = str_getcsv($api_result, '"" ""');

        return $output;
    }

    private function callScaleSerpAPISuggests($query): array
    {
        $apiKey = $this->params->get('SCALE_SERP_API_KEY');

        $queryString = http_build_query([
            'api_key' => $apiKey,
            'q' => $query,
            'output' => 'csv',
            'google_domain' => 'google.fr',
            'gl' => 'fr',
            'hl' => 'fr',
            'device' => 'desktop',
            'num' => '15',
            'csv_fields' => 'related_searches.query'
        ]);

        $ch = curl_init(sprintf('%s?%s', 'https://api.scaleserp.com/search', $queryString));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);

        $api_result = curl_exec($ch);
        curl_close($ch);

        $output = str_getcsv($api_result, '"" ""');

        return $output;
    }

    #[Route('project/{projectId}/universes/{universeId}/branches/{branchId}/ideas/{ideaId}', name: 'app_ideas_generate')]
    public function generate(ProjectsRepository $projectsRepository, SnippetsRepository $snippetsRepository, SuggestionsRepository $suggestionsRepository, IdeasRepository $ideasRepository, Request $request, EntityManagerInterface $entityManager, int $projectId, int $universeId, int $branchId, int $ideaId): Response
    {
        /** @var Users $user */
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $userId = $user->getId(); // Récupère l'ID de l'utilisateur connecté
        $countProjects = $projectsRepository->countByUserId($userId); // Récupère le nombre d'entrées
        $countSnippets = $snippetsRepository->countBySnippet($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countTrunc = $snippetsRepository->countByTrunc($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countSuggestions = $suggestionsRepository->countBySuggestion($userId); // Récupère le nombre de Snippets créés par cet utilisateur



        $idea = $ideasRepository->find($ideaId);
        if (!$idea) {
            throw new EntityNotFoundException('Idea with ID "' . $ideaId . '" does not exist.');
        }
        $ideaName = $idea->getName();

        $apiSnippets = $this->callScaleSerpAPISnippets($ideaName);

        foreach ($apiSnippets as $snippetText) {
            if (!$snippetsRepository->snippetExistsForIdea($snippetText, $ideaId)) { // vérifie si le snippet existe
                $snippet = new Snippets(); // créez une nouvelle instance de Snippet
                $snippet->setSnippet($snippetText); // définit le texte du snippet
                $snippet->setIdeas($idea); // lie le snippet à l'objet Idea

                $entityManager->persist($snippet); // prépare le snippet pour la sauvegarde
            }
        }

        $apiSuggests = $this->callScaleSerpAPISuggests($ideaName);

        foreach ($apiSuggests as $suggestion) {
            if (!$suggestionsRepository->suggestionExistsForIdea($suggestion, $ideaId)) { // vérifie si la suggestion existe
                $suggest = new Suggestions(); // créez une nouvelle instance de Suggestion
                $suggest->setSuggestion($suggestion); // définit le texte de la suggestion
                $suggest->setIdeas($idea); // lie la suggestion à l'objet Idea

                $entityManager->persist($suggest); // prépare la suggestion pour la sauvegarde
            }
        }

        $entityManager->flush(); // sauvegarde toutes les suggestions dans la base de données

        $results = [];

        for ($i = 0; $i < count($apiSnippets); $i++) {
            $snippet = $apiSnippets[$i];
            $suggestion = $i < count($apiSuggests) ? $apiSuggests[$i] : Null; // valeur par défaut

            $results[] = ['snippet' => $snippet, 'suggestion' => $suggestion];
        }

        return $this->render('ideas/scrap.html.twig', [
            'controller_name' => 'IdeasController',
            'projects' => $countProjects,
            'snippets' => $countSnippets,
            'truncated' => $countTrunc,
            'suggestions' => $countSuggestions,
            'projectId' => $projectId,
            'universeId' => $universeId,
            'branchId' => $branchId,
            'ideaId' => $ideaId,
            'results' => $results
        ]);
    }
}
