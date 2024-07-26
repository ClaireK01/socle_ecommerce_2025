<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('{_locale}/articles/list', name: 'app_article_list')]
    public function listAction(Request $request, ArticleRepository $articleRepository, PaginatorInterface $paginator): Response
    {
        $page = $request->get('page') ?? 1;
        $qb = $articleRepository->createQueryBuilder("a");
            //->where('e.deletedAt is null')
        ;
        $pagination = $paginator->paginate(
            $qb,
            $page,
            9
        );

        return $this->render('app/article/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('{_locale}/articles/{id}/show', name: 'app_article_show')]
    public function showAction(Article $article, Request $request,  ArticleRepository $articleRepository): Response
    {
        $similar = $articleRepository->findAll();

        return $this->render('app/article/show.html.twig', [
            'similarArticles' => $similar,
            'article' => $article,
        ]);
    }
}
