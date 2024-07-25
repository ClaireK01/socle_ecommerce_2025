<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'admin_article_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $properties = [
            'title',
        ];

        $filter = $request->get('filter');
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(Article::class, 'e')
        //->where('e.deletedAt is null')
        ;

        $pagination = $paginator->paginate(
            $qb,
            $page,
            10
        );

        return $this->render('/admin/list.html.twig', [
            'controller_name' => 'AppController',
            'pagination' => $pagination,
            'properties' => $properties,
            'class' => Article::class
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_article_edit')]
    #[Route('/new', name: 'admin_article_new')]
    public function editAction(Request $request, EntityManagerInterface $em, $id = null): Response
    {
        $article = $id != null && $em->getRepository(Article::class)->find($id) ? $em->getRepository(Article::class)->find($id) : new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('admin_article_index');
        }
        return $this->render('/admin/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);
    }

    #[ParamConverter('article', class: Article::class)]
    #[Route('/delete/{id}', name: 'admin_article_delete')]
    public function deleteAction(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('admin_article_index');
    }

}
