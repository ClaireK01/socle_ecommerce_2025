<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'admin_category_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $properties = [
            'title',
        ];
        $filter = $request->get('filter');
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(Category::class, 'e')
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
            'class' => Category::class
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_category_edit')]
    #[Route('/new', name: 'admin_category_new')]
    public function editAction(Request $request, EntityManagerInterface $em, $id = null): Response
    {
        $category = $id != null && $em->getRepository(Category::class)->find($id) ? $em->getRepository(Category::class)->find($id) : new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('admin_category_index');
        }
        return $this->render('/admin/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    #[ParamConverter('category', class: Category::class)]
    #[Route('/{id}/delete', name: 'admin_category_delete')]
    public function deleteAction(Category $category, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('admin_category_index');
    }

    #[ParamConverter('product', class: Category::class)]
    #[Route('/{id}/show', name: 'admin_product_show')]
    public function showAction(Category $category, Request $request, EntityManagerInterface $em): Response
    {
        return $this->render('/admin/show.html.twig', [
            'entity' => $category,
        ]);
    }

}
