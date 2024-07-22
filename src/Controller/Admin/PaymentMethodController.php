<?php

namespace App\Controller\Admin;

use App\Entity\PaymentMethod;
use App\Form\PaymentMethodType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/paymentmethod')]
class PaymentMethodController extends AbstractController
{
    #[Route('/', name: 'admin_paymentmethod_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $filter = $request->get('filter');
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(PaymentMethod::class, 'e')
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

    #[Route('/edit/{id}', name: 'admin_paymentmethod_edit')]
    #[Route('/new', name: 'admin_paymentmethod_new')]
    public function editAction(Request $request, EntityManagerInterface $em, $id = null): Response
    {
        $paymentmethod = $id != null && $em->getRepository(PaymentMethod::class)->find($id) ? $em->getRepository(PaymentMethod::class)->find($id) : new PaymentMethod();

        $form = $this->createForm(PaymentMethodType::class, $paymentmethod);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($paymentmethod);
            $em->flush();
            return $this->redirectToRoute('admin_paymentmethod_index');
        }
        return $this->render('/admin/edit.html.twig', [
            'paymentmethod' => $paymentmethod,
            'form' => $form->createView()
        ]);
    }

    #[ParamConverter('paymentmethod', class: PaymentMethod::class)]
    #[Route('/delete/{id}', name: 'admin_paymentmethod_delete')]
    public function deleteAction(PaymentMethod $paymentmethod, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($paymentmethod);
        $em->flush();

        return $this->redirectToRoute('admin_paymentmethod_index');
    }

}
