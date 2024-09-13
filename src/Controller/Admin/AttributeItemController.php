<?php

namespace App\Controller\Admin;

use App\Entity\AttributeItem;
use App\Form\AttributeItemType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/attributeitem')]
class AttributeItemController extends AbstractController
{
    #[Route('/', name: 'admin_attributeitem_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {

        $properties = [
        ];
        $filter = $request->get('filter');
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(AttributeItem::class, 'e')
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
            'class' => AttributeItem::class
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_attributeitem_edit')]
    #[Route('/new', name: 'admin_attributeitem_new')]
    public function editAction(Request $request, EntityManagerInterface $em, $id = null): Response
    {
        $attributeitem = $id != null && $em->getRepository(AttributeItem::class)->find($id) ? $em->getRepository(AttributeItem::class)->find($id) : new AttributeItem();

        $form = $this->createForm(AttributeItemType::class, $attributeitem);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($attributeitem);
            $em->flush();
            return $this->redirectToRoute('admin_attributeitem_index');
        }
        return $this->render('/admin/edit.html.twig', [
            'attributeitem' => $attributeitem,
            'form' => $form->createView()
        ]);
    }

    #[ParamConverter('attributeitem', class: AttributeItem::class)]
    #[Route('/{id}/delete', name: 'admin_attributeitem_delete')]
    public function deleteAction(AttributeItem $attributeitem, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($attributeitem);
        $em->flush();

        return $this->redirectToRoute('admin_attributeitem_index');
    }

    #[ParamConverter('attributeitem', class: AttributeItem::class)]
    #[Route('/{id}/show', name: 'admin_attributeitem_show')]
    public function showAction(AttributeItem $attributeitem, Request $request, EntityManagerInterface $em): Response
    {
        return $this->render('/admin/show.html.twig', [
            'entity' => $attributeitem,
        ]);
    }

}
