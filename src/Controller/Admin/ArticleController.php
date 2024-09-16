<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Service\AdminService;
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
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator, AdminService $adminService): Response
    {
        $properties = [
            'title',
        ];

        $filter = $request->request->all();
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(Article::class, 'e')
            ->join('e.translations', 'et')
        //->where('e.deletedAt is null')
        ;

        $qb = $adminService->filter($qb, $filter, 'e', 'et');


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

    #[Route('/{id}/edit', name: 'admin_article_edit')]
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
    #[Route('/{id}/delete', name: 'admin_article_delete')]
    public function deleteAction(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('admin_article_index');
    }


    #[ParamConverter('article', class: Article::class)]
    #[Route('/{id}/show', name: 'admin_article_show')]
    public function showAction(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        return $this->render('/admin/show.html.twig', [
            'entity' => $article,
        ]);
    }

}
