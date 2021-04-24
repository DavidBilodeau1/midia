<?php

namespace App\Service;

use Symfony\Component\Finder\Finder;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Book;
use App\Entity\ComicSeries;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\BookRepository;
use ZipArchive;
use lywzx\epub\EpubParser;

class BookLoader
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getBooks()
    {
            set_time_limit(0);
            $finder = new Finder();
            $finder->files()->name('*.epub')->name('*.pdf')->in('W:\Livres');
            if ($finder->hasResults()) {
                $books = $finder;
            }
            foreach ($books as $publication) {
                    $book = $this->em->getRepository(Book::class)->findOneBy(['path' => $publication->getPathname()]) ?: new Book();
                    $book->setPath($publication->getPathname());
                    $book->setName($publication->getFilename());
                    $extension = pathinfo(parse_url($publication->getPathname(), PHP_URL_PATH), PATHINFO_EXTENSION);
                    if($extension == 'epub'){
                        try{
                            $epubParser = new EpubParser($publication->getPathname());
                            $epubParser->parse();
                            if($epubParser->getDcItem()){
                                $book->setTitle($epubParser->getDcItem("title"));
                                $book->setAuthor($epubParser->getDcItem("creator"));
                            }
                        }
                        catch(\Exception $ex){
                            continue;
                        }
                    }
                    $this->em->persist($book);
                    $this->em->flush();

            }
    }
}
?>
