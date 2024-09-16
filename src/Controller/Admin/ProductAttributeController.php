<?php

namespace App\Controller\Admin;

use App\Entity\ProductAttribute;
use App\Form\ProductAttributeType;
use App\Service\AdminService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/productattribute')]
class ProductAttributeController extends AbstractController
{
    #[Route('/', name: 'admin_productattribute_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator, AdminService $adminService): Response
    {

        $properties = [
            'title',
        ];

        $filter = $request->request->all();
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(ProductAttribute::class, 'e')
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
            'class' => ProductAttribute::class
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_productattribute_edit')]
    #[Route('/new', name: 'admin_productattribute_new')]
    public function editAction(Request $request, EntityManagerInterface $em, $id = null): Response
    {
        $productattribute = $id != null && $em->getRepository(ProductAttribute::class)->find($id) ? $em->getRepository(ProductAttribute::class)->find($id) : new ProductAttribute();

        $form = $this->createForm(ProductAttributeType::class, $productattribute);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($productattribute);
            $em->flush();
            return $this->redirectToRoute('admin_productattribute_index');
        }
        return $this->render('/admin/edit.html.twig', [
            'productattribute' => $productattribute,
            'form' => $form->createView()
        ]);
    }

    #[ParamConverter('productattribute', class: ProductAttribute::class)]
    #[Route('/{id}/delete', name: 'admin_productattribute_delete')]
    public function deleteAction(ProductAttribute $productattribute, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($productattribute);
        $em->flush();

        return $this->redirectToRoute('admin_productattribute_index');
    }

    #[ParamConverter('productattribute', class: ProductAttribute::class)]
    #[Route('/{id}/show', name: 'admin_productattribute_show')]
    public function showAction(ProductAttribute $productattribute, Request $request, EntityManagerInterface $em): Response
    {
        return $this->render('/admin/show.html.twig', [
            'entity' => $productattribute,
        ]);
    }

}
