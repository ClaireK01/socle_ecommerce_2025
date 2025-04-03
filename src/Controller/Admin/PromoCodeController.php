<?php

namespace App\Controller\Admin;

use App\Entity\PromoCode;
use App\Form\PromoCodeType;
use App\Service\AdminService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/promocode')]
class PromoCodeController extends AbstractController
{
    #[Route('/', name: 'admin_promocode_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator, AdminService $adminService): Response
    {

        $properties = [
        ];
        $filter = $request->request->all();
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(PromoCode::class, 'e')

            //->where('e.deletedAt is null')
        ;

        $qb = $adminService->filter($qb, $filter, 'e' );

        $pagination = $paginator->paginate(
            $qb,
            $page,
            10
        );

        return $this->render('/admin/list.html.twig', [
            'controller_name' => 'AppController',
            'pagination' => $pagination,
            'properties' => $properties,
            'class' => PromoCode::class
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_promocode_edit')]
    #[Route('/new', name: 'admin_promocode_new')]
    public function editAction(Request $request, EntityManagerInterface $em, $id = null): Response
    {
        $promocode = $id != null && $em->getRepository(PromoCode::class)->find($id) ? $em->getRepository(PromoCode::class)->find($id) : new PromoCode();

        $form = $this->createForm(PromoCodeType::class, $promocode);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($promocode);
            $em->flush();
            return $this->redirectToRoute('admin_promocode_index');
        }
        return $this->render('/admin/edit.html.twig', [
            'promocode' => $promocode,
            'form' => $form->createView()
        ]);
    }

    #[ParamConverter('promocode', class: PromoCode::class)]
    #[Route('/{id}/delete', name: 'admin_promocode_delete')]
    public function deleteAction(PromoCode $promocode, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($promocode);
        $em->flush();

        return $this->redirectToRoute('admin_promocode_index');
    }

    #[ParamConverter('promocode', class: PromoCode::class)]
    #[Route('/{id}/show', name: 'admin_promocode_show')]
    public function showAction(PromoCode $promocode, Request $request, EntityManagerInterface $em): Response
    {
        return $this->render('/admin/show.html.twig', [
            'entity' => $promocode,
        ]);
    }

}
