<?php

namespace App\Controller\Admin;

use App\Entity\EcommerceOrder;
use App\Events\EcommerceOrderEvent;
use App\Events\Events;
use App\Events\MailEvent;
use App\Form\OrderType;
use App\Service\AdminService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'admin_order_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator, AdminService $adminService): Response
    {
        $properties = [
            'number',
            'statusLabel',
            ['shippingAddress' => ['user' => ['email' => 'email'] ] ],
            ['shippingAddress' => ['firstName' => 'firstName'] ],
            ['shippingAddress' => ['lastName' => 'lastName'] ],
        ];


        $filter = $request->request->all();
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(EcommerceOrder::class, 'e')
            ->join('e.shipping_address', 's')
            ->join('e.facturation_address', 'f')
            ->join('s.user', 'u')
        //->where('e.deletedAt is null')
        ;

        $qb = $adminService->filter($qb, $filter, 'e');

        $pagination = $paginator->paginate(
            $qb,
            $page,
            10
        );

        return $this->render('/admin/list.html.twig', [
            'controller_name' => 'AppController',
            'pagination' => $pagination,
            'properties' => $properties,
            'class' => EcommerceOrder::class
        ]);
    }

    #[ParamConverter('order', class: EcommerceOrder::class)]
    #[Route('/{id}/show', name: 'admin_order_show')]
    public function showAction(EcommerceOrder $order, Request $request, EntityManagerInterface $em, EventDispatcherInterface $dispatcher): Response
    {

       if($request->getMethod() == "POST"){
           $state = $request->request->get('state') ?? null;
           if($state){
               $dispatcher->dispatch(new EcommerceOrderEvent($order, $state, $request->getLocale()), Events::ORDER_STATE_CHANGED);
           }
           return $this->redirectToRoute('admin_order_index');
       }

        return $this->render('/admin/show_order.html.twig', [
            'entity' => $order,
        ]);
    }



//    #[Route('/{id}/edit', name: 'admin_order_edit')]
//    #[Route('/new', name: 'admin_order_new')]
//    public function editAction(Request $request, EntityManagerInterface $em, $id = null): Response
//    {
//        $order = $id != null && $em->getRepository(Order::class)->find($id) ? $em->getRepository(Order::class)->find($id) : new Order();
//
//        $form = $this->createForm(OrderType::class, $order);
//
//        $form->handleRequest($request);
//
//        if($form->isSubmitted() && $form->isValid()){
//            $em->persist($order);
//            $em->flush();
//            return $this->redirectToRoute('admin_order_index');
//        }
//        return $this->render('/admin/edit.html.twig', [
//            'order' => $order,
//            'form' => $form->createView()
//        ]);
//    }

//    #[ParamConverter('order', class: EcommerceOrder::class)]
//    #[Route('/{id}/delete', name: 'admin_order_delete')]
//    public function deleteAction(EcommerceOrder $order, Request $request, EntityManagerInterface $em): Response
//    {
//        $em->remove($order);
//        $em->flush();
//
//        return $this->redirectToRoute('admin_order_index');
//    }
}
