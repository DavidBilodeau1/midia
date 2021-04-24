<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\ComicHistory;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $historyRepository = $this->getDoctrine()->getRepository(ComicHistory::class);
        $comicHistories = $historyRepository->findByUser($user->getId());

        return $this->render('home/index.html.twig', array(
            'user' => $this->getUser(),
            'readingComics' => $comicHistories
        ));
    }
}
