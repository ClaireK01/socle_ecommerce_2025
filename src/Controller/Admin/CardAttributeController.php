<?php

namespace App\Controller\Admin;

use App\Entity\CardAttribute;
use App\Form\CardAttributeType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/cardattribute')]
class CardAttributeController extends AbstractController
{
    #[Route('/', name: 'admin_cardattribute_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {

        $properties = [
        ];
        $filter = $request->get('filter');
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(CardAttribute::class, 'e')
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
            'class' => CardAttribute::class
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_cardattribute_edit')]
    #[Route('/new', name: 'admin_cardattribute_new')]
    public function editAction(Request $request, EntityManagerInterface $em, $id = null): Response
    {
        $cardattribute = $id != null && $em->getRepository(CardAttribute::class)->find($id) ? $em->getRepository(CardAttribute::class)->find($id) : new CardAttribute();

        $form = $this->createForm(CardAttributeType::class, $cardattribute);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($cardattribute);
            $em->flush();
            return $this->redirectToRoute('admin_cardattribute_index');
        }
        return $this->render('/admin/edit.html.twig', [
            'cardattribute' => $cardattribute,
            'form' => $form->createView()
        ]);
    }

    #[ParamConverter('cardattribute', class: CardAttribute::class)]
    #[Route('/{id}/delete', name: 'admin_cardattribute_delete')]
    public function deleteAction(CardAttribute $cardattribute, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($cardattribute);
        $em->flush();

        return $this->redirectToRoute('admin_cardattribute_index');
    }

    #[ParamConverter('cardattribute', class: CardAttribute::class)]
    #[Route('/{id}/show', name: 'admin_cardattribute_show')]
    public function showAction(CardAttribute $cardattribute, Request $request, EntityManagerInterface $em): Response
    {
        return $this->render('/admin/show.html.twig', [
            'entity' => $cardattribute,
        ]);
    }

}
