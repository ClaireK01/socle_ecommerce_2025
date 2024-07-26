<?php

namespace App\Controller\Admin;

use App\Entity\Tva;
use App\Form\TvaType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/tva')]
class TvaController extends AbstractController
{
    #[Route('/', name: 'admin_tva_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {

        $properties = [
            'title'
        ];
        $filter = $request->get('filter');
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(Tva::class, 'e')
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
            'class' => Tva::class
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_tva_edit')]
    #[Route('/new', name: 'admin_tva_new')]
    public function editAction(Request $request, EntityManagerInterface $em, $id = null): Response
    {
        $tva = $id != null && $em->getRepository(Tva::class)->find($id) ? $em->getRepository(Tva::class)->find($id) : new Tva();

        $form = $this->createForm(TvaType::class, $tva);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($tva);
            $em->flush();
            return $this->redirectToRoute('admin_tva_index');
        }
        return $this->render('/admin/edit.html.twig', [
            'tva' => $tva,
            'form' => $form->createView()
        ]);
    }

    #[ParamConverter('tva', class: Tva::class)]
    #[Route('/{id}/delete', name: 'admin_tva_delete')]
    public function deleteAction(Tva $tva, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($tva);
        $em->flush();

        return $this->redirectToRoute('admin_tva_index');
    }

    #[ParamConverter('tva', class: Tva::class)]
    #[Route('/{id}/show', name: 'admin_tva_show')]
    public function showAction(Tva $tva, Request $request, EntityManagerInterface $em): Response
    {
        return $this->render('/admin/show.html.twig', [
            'entity' => $tva,
        ]);
    }

}
