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
use App\Entity\Book;
use lywzx\epub\EpubParser;
use Knp\Bundle\SnappyBundle\KnpSnappyBundle;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

class BookController extends AbstractController
{
    /**
     * @Route("/books/{page}", name="books")
     */
    public function index(Request $request, $page = 1)
    {
        $booksRepository = $this->getDoctrine()->getRepository(Book::class);
        $books = $booksRepository->findAllPaginated($page);
        $totalPostsReturned = $books->getIterator()->count();
        $totalPosts = $books->count();
        $limit = 15;
        $maxPages = ceil($totalPosts / $limit);
        return $this->render('books/index.html.twig', [
            'currentPath' => 'books',
            'books' => $books,
            'maxPages' => $maxPages,
            'page' => $page
        ]);
    }

    /**
     * @Route("/book/{book_id}", name="book")
     */
    public function preview(Request $request, \Knp\Snappy\Pdf $snappy, $book_id)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find(['id' => $book_id]) ?: new Book();
        $extension = pathinfo(parse_url($book->getPath(), PHP_URL_PATH), PATHINFO_EXTENSION);
        $content = "";
        if($extension == 'epub'){
            try{
                $epubParser = new EpubParser($book->getPath());
                $epubParser->parse();
                $chapters = array_keys($epubParser->getManifestByType('application/xhtml+xml'));
                foreach ($chapters as $chapter) {
                    $content .= $epubParser->getChapter($chapter);
                }
            }
            catch(\Exception $ex){
                die('oops');
            }
            $options = array(
                'header-spacing' => '0',
                'margin-left' => '3mm',
                'margin-right' => '3mm',
                'margin-top' => '5mm',
                'margin-bottom' => '10mm',
                'page-size' => 'Letter',
                'enable-local-file-access' => true
            );
        }
        return  new PdfResponse(
            $snappy->getOutputFromHtml(
                '<header><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></header><body>' . $content . '</body>',
                $options
            ),
            'filename.pdf'
        );
        return $this->render('books/preview.html.twig', [
            'book' => $book,
            'content' => $pdf
        ]);
    }
}
