<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\RouterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(requirements: ['_locale' => 'en|fr'])]
class AppController extends AbstractController
{
    #[Route('/{_locale}/', name: 'app_homepage', options: ["sitemap" => true ])]
    public function index(Request $request): Response
    {
        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/{_locale}/about', name: 'app_presentation', options: ["sitemap" => true, 'breadcrumb' => ['name' => 'about']])]
    public function aboutAction(Request $request): Response
    {
        return $this->render('app/presentation.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/{_locale}/sitemap', name: 'app_sitemap', options: ["sitemap" => true, 'breadcrumb' => ['name' => 'sitemap']])]
    public function sitemapAction(Request $request, RouterService $service): Response
    {
        return $this->render('app/sitemap.html.twig', [
            'routes' =>  $service->sitemap(),
        ]);
    }
}
