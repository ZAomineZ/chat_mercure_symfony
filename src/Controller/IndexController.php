<?php

namespace App\Controller;

use App\Mercure\CookieGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    /**
     * @param CookieGenerator $cookieGenerator
     * @return Response
     */
    #[Route('/', name: 'home')]
    public function index(CookieGenerator $cookieGenerator): Response
    {
        $response =  $this->render('index/index.html.twig');
        $response->headers->setCookie($cookieGenerator->generate());

        return $response;
    }
}
