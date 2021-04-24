<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpClient\HttpClient;
use App\Entity\ComicSeries;
use App\Entity\Comic;
use App\Entity\ComicHistory;
use App\Entity\User;
use ZipArchive;
use RarArchive;
use RarException;
use App\Api\ComicConnector;

class ComicController extends AbstractController
{
    /**
     * @Route("/series/{page}", name="series")
     */
    public function index(Request $request, $page = 1)
    {
        $seriesRepository = $this->getDoctrine()->getRepository(ComicSeries::class);
        $series = $seriesRepository->findAllPaginated($page);
        $totalPostsReturned = $series->getIterator()->count();
        $totalPosts = $series->count();
        $limit = 6;
        $maxPages = ceil($totalPosts / $limit);
        return $this->render('comics/index.html.twig', [
            'currentPath' => 'series',
            'comics' => $series,
            'maxPages' => $maxPages,
            'page' => $page
        ]);
    }

    /**
     * @Route("/comics/{series_id}/{page}", name="comics")
     */
    public function comics(Request $request, $series_id = null, $page = 1)
    {
        $seriesRepository = $this->getDoctrine()->getRepository(ComicSeries::class);
        $serie = $seriesRepository->find(['id' => $series_id]);
        $comicRepository = $this->getDoctrine()->getRepository(Comic::class);
        $offset = ($page-1)*6;
        $comics = $comicRepository->findBy(['series' => $serie->getId()], ['name' => 'ASC'], 6, $offset);
        $totalPostsReturned = count($comicRepository->findBy(['series' => $serie->getId()]));
        $totalPosts = count($comics);
        foreach ($comics as $comic) {
            $comicHistoryRepository = $this->getDoctrine()->getRepository(ComicHistory::class);
            $user = $this->getUser();
            $comic = $comicRepository->find(['id' => $comic->getId()]);
            $comicHistory = $comicHistoryRepository->findOneBy(['user' => $user->getId(), 'comic' => $comic->getId()]);
            if($comicHistory){
                $comic->progress = $comicHistory->getPage();
            }
        }
        $limit = 6;
        $maxPages = ceil($totalPostsReturned / $limit);
        return $this->render('comics/list.html.twig', [
            'currentPath' => 'comics',
            'serie' => $serie,
            'comics' => $comics,
            'maxPages' => $maxPages,
            'page' => $page
        ]);
    }

    /**
     * @Route("/save-page/{page_id}/{comic_id}", name="savePage")
     */
    public function savePage(Request $request, $page_id = 0, $comic_id = null)
    {
        $comicRepository = $this->getDoctrine()->getRepository(Comic::class);
        $comicHistoryRepository = $this->getDoctrine()->getRepository(ComicHistory::class);
        $user = $this->getUser();
        $comic = $comicRepository->find(['id' => $comic_id]);
        $comicHistory = $comicHistoryRepository->findOneBy(['user' => $user->getId(), 'comic' => $comic->getId()]);
        if($comicHistory == null){
            $history = new ComicHistory();
        }
        else{
            $history = $comicHistory;
        }
        $history->setUser($user);
        $history->setComic($comic);
        $history->setPage($page_id);
        $this->getDoctrine()->getManager()->persist($history);
        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * @Route("/comic/{comic_id}/{page}", name="comic", defaults={"page"=1})
     */
    public function comic(Request $request, $comic_id = null, $page)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit','1024M');
        $comicRepository = $this->getDoctrine()->getRepository(Comic::class);
        $comic = $comicRepository->find(['id' => $comic_id]);
        $seriesRepository = $this->getDoctrine()->getRepository(ComicSeries::class);
        $serie = $seriesRepository->find(['id' => $comic->getSeries()]);
        $user = $this->getUser();
        $historyRepository = $this->getDoctrine()->getRepository(ComicHistory::class);
        $history = $historyRepository->findOneBy(['user' => $user->getId(), 'comic' => $comic->getId()]);
        $currentPage = $history ? $history->getPage() : 0;
        $extension = pathinfo(parse_url($comic->getAbsolutePath(), PHP_URL_PATH), PATHINFO_EXTENSION);
        $pages = [];
                try {
                    RarException::setUsingExceptions(true);
                    $rar_file = RarArchive::open($comic->getAbsolutePath());
                    $entries = $rar_file->getEntries();
                    foreach ($entries as $entry) {
                        $name = str_replace('\\', '/', $entry->getName());
                        $entryPath = 'tmp/' . $comic->getId() . '/' . $entry->getName();
                        $pathInfo = pathinfo(parse_url($entryPath, PHP_URL_PATH), PATHINFO_EXTENSION);
                        if(!file_exists($entryPath)){
                            $entry->extract('tmp/' . $comic->getId() . '/');
                        }
                        if(strtolower($pathInfo) == "jpg" || strtolower($pathInfo) == "png" || strtolower($pathInfo) == "gif"){
                            $pages[] = $entryPath;
                            $size = getimagesize($entryPath);
                            $double[] = ($size[0] > $size[1]);
                        }
                    }
                } catch (RarException $e) {
                    try {
                        $zipArchive = new ZipArchive();
                        if($zipArchive->open($comic->getAbsolutePath())){
                            $index = $zipArchive->numFiles;
                            $zipArchive->extractTo('tmp/' . $comic->getId() . '/');
                            for($i = 0; $i < $index; $i++) {
                                $entryPath = 'tmp/' . $comic->getId() . '/' . $zipArchive->getNameIndex($i);
                                $pathInfo = pathinfo(parse_url($entryPath, PHP_URL_PATH), PATHINFO_EXTENSION);
                                if(strtolower($pathInfo) == "jpg" || strtolower($pathInfo) == "png" || strtolower($pathInfo) == "gif"){
                                    $pages[] = $entryPath;
                                    $size = getimagesize($entryPath);
                                    $double[] = ($size[0] > $size[1]);
                                }
                            }
                            $zipArchive->close();
                        }
                    }
                    catch (RarException $e) {
                        die('MAUVAIS FORMAT');
                    }

                }
        sort($pages);
        $comic->setTotalPages(count($pages));
        $this->getDoctrine()->getManager()->persist($comic);
        $this->getDoctrine()->getManager()->flush();
        return $this->render('comics/preview.html.twig', [
            'serie' => $serie,
            'comic' => $comic,
            'pages' => $pages,
            'totalPages' => $comic->getTotalPages(),
            'double' => $double,
            'defaultPage' => isset($page) ? $page : $currentPage
        ]);
    }
    /**
     * @Route("/series-infos/{page}", name="getSeriesInfos")
     */
    public function getSeriesInfos($page = 1){
        $seriesRepository = $this->getDoctrine()->getRepository(ComicSeries::class);
        $series = $seriesRepository->findAll();
        $comicConnector = new ComicConnector();
        $responses = [];
        foreach ($series as $serie) {
            $object = json_decode($comicConnector->search($serie->getTitle()));
            $serie->setImage($object->results[0]->image->small_url);
            if(isset($object->results[0])){
                $serie->setTitle($object->results[0]->name);
                $serie->setApiDetailUrl($object->results[0]->api_detail_url);
            }
            $this->getDoctrine()->getManager()->persist($serie);
            $this->getDoctrine()->getManager()->flush();
            $volume = json_decode($comicConnector->details($serie->getApiDetailUrl()));
            $this->getComicsInfos($serie, $volume);
        }
        $series = $seriesRepository->findAllPaginated($page);
        $totalPostsReturned = $series->getIterator()->count();
        $totalPosts = $series->count();
        $limit = 6;
        $maxPages = ceil($totalPosts / $limit);
        return $this->render('comics/index.html.twig', [
            'currentPath' => 'series',
            'comics' => $series,
            'maxPages' => $maxPages,
            'page' => $page
        ]);
    }
    /**
     * @Route("/get-matches/{series_id}/{query}", name="getMatches")
     */
    public function getMatches($series_id, $query=null){
        $seriesRepository = $this->getDoctrine()->getRepository(ComicSeries::class);
        $serie = $seriesRepository->find(['id' => $series_id]);
        $comicConnector = new ComicConnector();
        $responses = [];
        if($query != null){
            $object = json_decode($comicConnector->search($query));
        }
        else{
            $object = json_decode($comicConnector->search($serie->getTitle()));
        }
        $response = new Response();
        $response->setContent(json_encode($object->results));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/series-match/{series_id}/{page}/{result?}", methods={"POST"}, name="correctMatch")
     */
    public function correctMatch(Request $request, $series_id, $page = 1, $result){
        $result = json_decode($request->request->get('result'));
        $seriesRepository = $this->getDoctrine()->getRepository(ComicSeries::class);
        $comicConnector = new ComicConnector();
        $serie = $seriesRepository->find(['id' => $series_id]);
        $serie->setImage($result->image->small_url);
        if($result->resource_type == "volume"){
            $serie->setTitle($result->name);
            $serie->setApiDetailUrl($result->api_detail_url);
        }
        else if(isset($result->volume)){
            $serie->setTitle($result->volume->name);
            $serie->setApiDetailUrl($result->volume->api_detail_url);
        }
        $this->getDoctrine()->getManager()->persist($serie);
        $this->getDoctrine()->getManager()->flush();
        $volume = json_decode($comicConnector->details($serie->getApiDetailUrl()));
        $this->getComicsInfos($serie, $volume);
        $series = $seriesRepository->findAllPaginated($page);
        $totalPostsReturned = $series->getIterator()->count();
        $totalPosts = $series->count();
        $limit = 6;
        $maxPages = ceil($totalPosts / $limit);
        return $this->render('comics/index.html.twig', [
            'currentPath' => 'series',
            'comics' => $series,
            'maxPages' => $maxPages,
            'page' => $page
        ]);
    }

    /**
     * @Route("/series-infos", name="getComicsInfos")
     */
    public function getComicsInfos($serie, $volume){
            $object = $volume;
            $comics = $serie->getComics();
            foreach ($comics as $key => $comic) {
                $issue = isset($object->results->issues[$key]) ? $object->results->issues[$key] : null;
                if($issue == null){
                    continue;
                }
                if($issue->name){
                    $comic->setTitle($issue->name);
                }
                if($issue->issue_number){
                    $comic->setNumber($issue->issue_number);
                }
                if($issue->api_detail_url){
                    $comic->setApiDetailUrl($issue->api_detail_url);
                }
                $this->getDoctrine()->getManager()->persist($comic);
                $this->getDoctrine()->getManager()->flush();
            }
    }

}
