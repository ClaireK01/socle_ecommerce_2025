<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(requirements: ['_locale' => 'en|fr'])]
class AppController extends AbstractController
{
    #[Route('/{_locale}/', name: 'app_homepage')]
    public function index(Request $request): Response
    {
        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/{_locale}/about', name: 'app_presentation', options: ['breadcrumb' => ['name' => 'about']])]
    public function aboutAction(Request $request): Response
    {
        return $this->render('app/presentation.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }
}
