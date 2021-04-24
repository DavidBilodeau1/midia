<?php

namespace App\Service;

use Symfony\Component\Finder\Finder;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Comic;
use App\Entity\ComicSeries;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\ComicSeriesRepository;
use App\Repository\ComicRepository;
use ZipArchive;

class ComicLoader
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getSeries()
    {
        set_time_limit(0);
        $finder = new Finder();
        $finder->depth('== 0');
        $finder->directories()->in('G:\livres\Marvel');
        if ($finder->hasResults()) {
            $folders = $finder;
        }
        foreach ($folders as $serie) {
            $series = $this->em->getRepository(ComicSeries::class)->findOneBy(['path' => $serie->getPathname()]);
            if($series == null){
                $comicSeries = new ComicSeries();
                $comicSeries->setTitle($serie->getFilename());
                $comicSeries->setPath($serie->getPathname());
                $this->em->persist($comicSeries);
                $this->em->flush();
            }
        }
        $this->getComics();
    }

    public function getComics()
    {
        set_time_limit(0);
        $series = $this->em->getRepository(ComicSeries::class)->findAll();

        foreach ($series as $serie) {
            $finder = new Finder();
            $finder->files()->name('*.cbr')->name('*.cbz')->in($serie->getPath());
            if ($finder->hasResults()) {
                $issues = $finder;
            }
            foreach ($issues as $issue) {
                $comic = $this->em->getRepository(Comic::class)->findOneBy(['name' => $issue->getFilename()]) ?: new Comic();
                    $comic->setName($issue->getFilename());
                    $comic->setSeries($serie);
                    $comic->setAbsolutePath($issue->getPathname());
                    $extension = pathinfo(parse_url($issue->getPathname(), PHP_URL_PATH), PATHINFO_EXTENSION);
                    if($extension == 'cbr'){
                        $rar_file = rar_open($issue->getPathname()) or die("Can't open Rar archive");
                        $entries = $rar_file->getEntries();
                        $comic->setTotalPages(count($entries));
                    }
                    if($extension == 'cbz'){
                        $zipArchive = new ZipArchive();
                        $zipArchive->open($issue->getPathname());
                        $index = $zipArchive->numFiles;
                        $comic->setTotalPages($index);
                    }
                    $this->em->persist($comic);
                    $this->em->flush();

            }
        }
    }
}
?>
