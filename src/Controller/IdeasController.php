<?php

namespace App\Controller;

use App\Entity\Snippets;
use App\Entity\Suggestions;
use App\Entity\Ideas;
use App\Entity\Branches;
use App\Entity\Synonyms;
use App\Entity\Users;
use App\Entity\Projects;
use App\Entity\Universes;
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
use App\Repository\SynonymsRepository;
use App\Repository\IdeasRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class IdeasController extends AbstractController
{
    #[Route('project/{projectId}/universes/{universeId}/branches/{branchId}/ideas', name: 'app_ideas')]
    public function index(ProjectsRepository $projectsRepository, SnippetsRepository $snippetsRepository, SuggestionsRepository $suggestionsRepository, IdeasRepository $ideasRepository, Request $request, EntityManagerInterface $entityManager, Breadcrumbs $breadcrumbs, int $projectId, int $universeId, int $branchId): Response
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

        $breadcrumbs->addItem($project->getName(), $this->generateUrl('app_projects_main', ['projectId' => $projectId]));
        $breadcrumbs->addItem($universe->getName(), $this->generateUrl('app_universes', ['projectId' => $projectId, 'universeId' => $universeId]));
        $breadcrumbs->addItem($branch->getName());

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
            'num' => '5',
            'csv_fields' => 'organic_results.snippet'
        ]);

        $ch = curl_init(sprintf('%s?%s', 'https://api.scaleserp.com/search', $queryString));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);

        $apiResult = curl_exec($ch);
        curl_close($ch);

        $output = str_getcsv($apiResult, '"" ""');

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

        $apiResult = curl_exec($ch);
        curl_close($ch);

        $output = str_getcsv($apiResult, '"" ""');

        return $output;
    }

    public function replace_content_inside_delimiters($start, $end, $new, $Ligne)
    {
        return preg_replace('#(' . preg_quote($start) . ')(.*)(' . preg_quote($end) . ')#si', '$1' . $new . '$3', $Ligne);
    }

    public function synonyms($motCle)
    {
        $motCle = str_replace(" ", "+", $motCle);
        $urlRecherche = "https://crisco4.unicaen.fr/des/synonymes/" . $motCle;

        $codeHtml = file_get_contents($urlRecherche);

        $start = "<!DOCTYPE";
        $end = '<div id="cliques">';
        $new = "";

        $nettoyage = $this->replace_content_inside_delimiters($start, $end, $new, $codeHtml);

        $start = '<div id="mention">';
        $end = '</html>';
        $new = "";

        $nettoyage = $this->replace_content_inside_delimiters($start, $end, $new, $nettoyage);

        $synoList = explode('<li>', $nettoyage);

        $synoStock = array();
        $p = 0;

        for ($i = 0; $i < sizeof($synoList); $i++) {
            if ($i != 0) {

                $rawText = strip_tags($synoList[$i]);

                $synoStock[$p] = $rawText;
                $p++;
            }
        }
        return $synoStock;
    }

    #[Route('project/{projectId}/universes/{universeId}/branches/{branchId}/ideas/{ideaId}', name: 'app_ideas_generate')]
    public function generate(ProjectsRepository $projectsRepository, SnippetsRepository $snippetsRepository, SuggestionsRepository $suggestionsRepository, SynonymsRepository $synonymsRepository, IdeasRepository $ideasRepository, EntityManagerInterface $entityManager, Breadcrumbs $breadcrumbs, int $projectId, int $universeId, int $branchId, int $ideaId): Response
    {
        /** @var Users $user */
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $userId = $user->getId(); // Récupère l'ID de l'utilisateur connecté
        $countProjects = $projectsRepository->countByUserId($userId); // Récupère le nombre d'entrées
        $countSnippets = $snippetsRepository->countBySnippet($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countTrunc = $snippetsRepository->countByTrunc($userId); // Récupère le nombre de Snippets créés par cet utilisateur
        $countSuggestions = $suggestionsRepository->countBySuggestion($userId); // Récupère le nombre de Snippets créés par cet utilisateur

        $idea = $ideasRepository->find($ideaId);
        $project = $entityManager->getRepository(Projects::class)->find($projectId);
        $universe = $entityManager->getRepository(Universes::class)->find($universeId);
        $branch = $entityManager->getRepository(Branches::class)->find($branchId);

        if (!$idea) {
            throw new EntityNotFoundException('Idea with ID "' . $ideaId . '" does not exist.');
        }
        if (!$project) {
            throw new EntityNotFoundException('Project with ID "' . $projectId . '" does not exist.');
        }
        if (!$universe) {
            throw new EntityNotFoundException('Universe with ID "' . $universeId . '" does not exist.');
        }
        if (!$branch) {
            throw new EntityNotFoundException('Branch with ID "' . $branchId . '" does not exist.');
        }

        $breadcrumbs->addItem($project->getName(), $this->generateUrl('app_projects_main', ['projectId' => $projectId]));
        $breadcrumbs->addItem($universe->getName(), $this->generateUrl('app_universes', ['projectId' => $projectId, 'universeId' => $universeId]));
        $breadcrumbs->addItem($branch->getName(), $this->generateUrl('app_branches', ['projectId' => $projectId, 'universeId' => $universeId, 'branchID' => $branchId]));
        $breadcrumbs->addItem($idea->getName());

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

        $synonyms = $this->synonyms($ideaName); // Récupérer les synonymes

        foreach ($synonyms as $synonym) {
            if (!$synonymsRepository->synonymExistsForIdea($synonym, $ideaId)) { // vérifie si la synonym existe
                $syn = new Synonyms(); // créez une nouvelle instance de Suggestion
                $syn->setSynonym($synonym); // définit le texte de la synonym
                $syn->setIdeas($idea); // lie la synonym à l'objet Idea

                $entityManager->persist($syn); // prépare la synonym pour la sauvegarde
            }
        }


        $entityManager->flush(); // sauvegarde toutes les suggestions dans la base de données

        $snippetsRepository->deleteSpecificSnippets('');
        $snippetsRepository->deleteSpecificSnippets('organic_results.snippet');
        $suggestionsRepository->deleteSpecificSuggestions('related_searches.query');

        $results = [];

        $maxLength = max(count($apiSnippets), count($apiSuggests), count($synonyms));

        for ($i = 0; $i < $maxLength; $i++) {
            $snippet = $i < count($apiSnippets) ? $apiSnippets[$i] : Null; // valeur par défaut
            $suggestion = $i < count($apiSuggests) ? $apiSuggests[$i] : Null; // valeur par défaut
            $synonym = $i < count($synonyms) ? $synonyms[$i] : Null; // valeur par défaut

            $results[] = ['snippet' => $snippet, 'suggestion' => $suggestion, 'synonym' => $synonym];
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
