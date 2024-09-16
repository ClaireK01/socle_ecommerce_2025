<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Service\AdminService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

#[Route('/admin/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'admin_product_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator, AdminService $adminService): Response
    {
        $properties = [
            'title',
            'price',
        ];

        $filter = $request->request->all();
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(Product::class, 'e')
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
            'class' => Product::class
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_product_edit')]
    #[Route('/new', name: 'admin_product_new')]
    public function editAction(Request $request, EntityManagerInterface $em, $id = null): Response
    {
        $product = $id != null && $em->getRepository(Product::class)->find($id) ? $em->getRepository(Product::class)->find($id) : new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('admin_product_index');
        }
        return $this->render('/admin/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    #[ParamConverter('product', class: Product::class)]
    #[Route('/{id}/delete', name: 'admin_product_delete')]
    public function deleteAction(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('admin_product_index');
    }

    #[ParamConverter('product', class: Product::class)]
    #[Route('/{id}/show', name: 'admin_product_show')]
    public function showAction(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        return $this->render('/admin/show.html.twig', [
            'entity' => $product,
        ]);
    }

}
