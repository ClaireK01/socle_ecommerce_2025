<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\ProductRepository;
use App\Service\RouterService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(requirements: ['_locale' => 'en|fr'])]
class AppController extends AbstractController
{
//    #[Route('/{_locale}/', name: 'app_homepage', options: ["sitemap" => true ])]
//    public function index(Request $request): Response
//    {
//        return $this->render('app/index.html.twig', [
//            'controller_name' => 'AppController',
//        ]);
//    }

    #[Route('/{_locale}/', name: 'app_homepage', options: ["sitemap" => true])]
    public function index(): Response
    {
        return $this->render('app_front/index.html.twig', [
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

    #[Route('/{_locale}/arts', name: 'app_art_list', options: ["breadcrumb" => "art"])]
    public function listArtAction(): Response
    {
        return $this->render('app_front/art/list.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/{_locale}/{id}/art/show', name: 'app_art_show')]
    public function showArtAction(): Response
    {
        return $this->render('app_front/art/show.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/{_locale}/artists', name: 'app_artist_list', options: ["breadcrumb" => "artist"])]
    public function listArtistAction(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request, ArticleRepository $articleRepository): Response
    {
        $pagination = $paginator->paginate(
            $articleRepository->getArticlesQuery(),
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('app_front/artist/list.html.twig', [
            'controller_name' => 'AppController',
            'pagination' => $pagination,
        ]);
    }

    #[Route('/{_locale}/{id}/artist/show', name: 'app_artist_show')]
    public function showArtistAction(): Response
    {
        return $this->render('app_front/artist/show.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/{_locale}/offers', name: 'app_offer_list', options: ["breadcrumb" => "offers"])]
    public function listOfferAction(): Response
    {
        return $this->render('app_front/offer/list.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/{_locale}/{id}/offer/show', name: 'app_offer_show')]
    public function showOfferAction(): Response
    {
        return $this->render('app_front/offer/show.html.twig', [
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
