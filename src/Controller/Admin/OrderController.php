<?php

namespace App\Controller\Admin;

use App\Entity\EcommerceOrder;
use App\Form\OrderType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'admin_order_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $properties = [
            'number',
            'status',
            ['shippingAddress' => ['user' => ['email' => 'email'] ] ],
            ['shippingAddress' => ['firstName' => 'firstName'] ],
            ['shippingAddress' => ['lastName' => 'lastName'] ],
        ];


        $filter = $request->get('filter');
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(EcommerceOrder::class, 'e')
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
            'class' => EcommerceOrder::class
        ]);
    }

    #[ParamConverter('order', class: EcommerceOrder::class)]
    #[Route('/{id}/show', name: 'admin_order_show')]
    public function showAction(EcommerceOrder $order, Request $request, EntityManagerInterface $em): Response
    {

       if($request->getMethod() == "POST"){
           $state = $request->request->get('state') ?? null;

           if($state){
               if($order->getStatus() != EcommerceOrder::STATES['canceled']){
                   //envoyer mails
                   if($state == EcommerceOrder::STATES['confirmed']){
                       if($order->getStatus() == EcommerceOrder::STATES['payed']) {
                           $order->setStatus($state);
                       }
                   }elseif ($state == EcommerceOrder::STATES['shipped']){
                       if($order->getStatus() == EcommerceOrder::STATES['confirmed']){
                           $order->setStatus($state);
                       }
                   }elseif ($state == EcommerceOrder::STATES['done']){
                       if($order->getStatus() == EcommerceOrder::STATES['shipped']){
                           $order->setStatus($state);
                       }
                   }elseif ($state == EcommerceOrder::STATES['canceled']){
                       $order->setStatus($state);
                   }
               }

               $order->setStatus($state);
               $em->persist($order);
               $em->flush();
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
