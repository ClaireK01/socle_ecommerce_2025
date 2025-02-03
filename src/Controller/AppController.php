<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\RouterService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

#[Route(requirements: ['_locale' => 'en|fr'])]
class AppController extends AbstractController
{
    #[Route('/{_locale}/', name: 'app_homepage', options: ["sitemap" => true])]
    public function index(ProductRepository $productRepository, UserRepository $userRepository, UserPasswordHasherInterface $hasher, EntityManagerInterface$entityManager): Response
    {
        $products = $productRepository->findAll();
        $hightlights1 = $productRepository->getProductHighLights("createdAt", 'ASC', "Grooming");

        return $this->render('app/index.html.twig', [
            'products' => $products,
            'hightlights1' => $hightlights1,
        ]);
    }

    #[Route('/{_locale}/about', name: 'app_presentation', options: ["sitemap" => true, 'breadcrumb' => ['name' => 'about']])]
    public function aboutAction(Request $request): Response
    {
        return $this->render('app/presentation.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/{_locale}/watch', name: 'app_watch_presentation', options: ["sitemap" => true, 'breadcrumb' => ['name' => 'about']])]
    public function watchAction(Request $request): Response
    {
        return $this->render('app/app_watch.html.twig', []);
    }


    #[Route('/{_locale}/sitemap', name: 'app_sitemap', options: ["sitemap" => true, 'breadcrumb' => ['name' => 'sitemap']])]
    public function sitemapAction(Request $request, RouterService $service): Response
    {
        return $this->render('app/sitemap.html.twig', [
            'routes' =>  $service->sitemap(),
        ]);
    }
}
