<?php

namespace App\Controller\Admin;

use App\Entity\Shipping;
use App\Form\ShippingType;
use App\Service\AdminService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/shipping')]
class ShippingController extends AbstractController
{
    #[Route('/', name: 'admin_shipping_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator, AdminService $adminService): Response
    {
        $properties = [
            'title',
        ];
        $filter = $request->request->all();
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(Shipping::class, 'e')
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
            'class' => Shipping::class
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_shipping_edit')]
    #[Route('/new', name: 'admin_shipping_new')]
    public function editAction(Request $request, EntityManagerInterface $em, $id = null): Response
    {
        $shipping = $id != null && $em->getRepository(Shipping::class)->find($id) ? $em->getRepository(Shipping::class)->find($id) : new Shipping();

        $form = $this->createForm(ShippingType::class, $shipping);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($shipping);
            $em->flush();
            return $this->redirectToRoute('admin_shipping_index');
        }
        return $this->render('/admin/edit.html.twig', [
            'shipping' => $shipping,
            'form' => $form->createView()
        ]);
    }

    #[ParamConverter('shipping', class: Shipping::class)]
    #[Route('/{id}/delete', name: 'admin_shipping_delete')]
    public function deleteAction(Shipping $shipping, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($shipping);
        $em->flush();

        return $this->redirectToRoute('admin_shipping_index');
    }

    #[ParamConverter('shipping', class: Shipping::class)]
    #[Route('/{id}/show', name: 'admin_shipping_show')]
    public function showAction(Shipping $shipping, Request $request, EntityManagerInterface $em): Response
    {
        return $this->render('/admin/show.html.twig', [
            'entity' => $shipping,
        ]);
    }

}
