<?php

namespace App\Controller\Admin;

use App\Entity\Cart;
use App\Form\CartType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'admin_cart_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $filter = $request->get('filter');
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(Cart::class, 'e')
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
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_cart_edit')]
    #[Route('/new', name: 'admin_cart_new')]
    public function editAction(Request $request, EntityManagerInterface $em, $id = null): Response
    {
        $cart = $id != null && $em->getRepository(Cart::class)->find($id) ? $em->getRepository(Cart::class)->find($id) : new Cart();

        $form = $this->createForm(CartType::class, $cart);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($cart);
            $em->flush();
            return $this->redirectToRoute('admin_cart_index');
        }
        return $this->render('/admin/edit.html.twig', [
            'cart' => $cart,
            'form' => $form->createView()
        ]);
    }

    #[ParamConverter('cart', class: Cart::class)]
    #[Route('/delete/{id}', name: 'admin_cart_delete')]
    public function deleteAction(Cart $cart, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($cart);
        $em->flush();

        return $this->redirectToRoute('admin_cart_index');
    }

}
