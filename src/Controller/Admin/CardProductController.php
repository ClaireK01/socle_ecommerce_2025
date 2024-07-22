<?php

namespace App\Controller\Admin;

use App\Entity\CardProduct;
use App\Form\CardProductType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/cardproduct')]
class CardProductController extends AbstractController
{
    #[Route('/', name: 'admin_cardproduct_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $filter = $request->get('filter');
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(CardProduct::class, 'e')
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

    #[Route('/edit/{id}', name: 'admin_cardproduct_edit')]
    #[Route('/new', name: 'admin_cardproduct_new')]
    public function editAction(Request $request, EntityManagerInterface $em, $id = null): Response
    {
        $cardproduct = $id != null && $em->getRepository(CardProduct::class)->find($id) ? $em->getRepository(CardProduct::class)->find($id) : new CardProduct();

        $form = $this->createForm(CardProductType::class, $cardproduct);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($cardproduct);
            $em->flush();
            return $this->redirectToRoute('admin_cardproduct_index');
        }
        return $this->render('/admin/edit.html.twig', [
            'cardproduct' => $cardproduct,
            'form' => $form->createView()
        ]);
    }

    #[ParamConverter('cardproduct', class: CardProduct::class)]
    #[Route('/delete/{id}', name: 'admin_cardproduct_delete')]
    public function deleteAction(CardProduct $cardproduct, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($cardproduct);
        $em->flush();

        return $this->redirectToRoute('admin_cardproduct_index');
    }

}
