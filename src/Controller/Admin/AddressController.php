<?php

namespace App\Controller\Admin;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/address')]
class AddressController extends AbstractController
{
    #[Route('/', name: 'admin_address_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $filter = $request->get('filter');
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(Address::class, 'e')
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

    #[Route('/edit/{id}', name: 'admin_address_edit')]
    #[Route('/new', name: 'admin_address_new')]
    public function editAction(Request $request, EntityManagerInterface $em, $id = null): Response
    {
        $address = $id != null && $em->getRepository(Address::class)->find($id) ? $em->getRepository(Address::class)->find($id) : new Address();

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($address);
            $em->flush();
            return $this->redirectToRoute('admin_address_index');
        }
        return $this->render('/admin/edit.html.twig', [
            'address' => $address,
            'form' => $form->createView()
        ]);
    }

    #[ParamConverter('address', class: Address::class)]
    #[Route('/delete/{id}', name: 'admin_address_delete')]
    public function deleteAction(Address $address, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($address);
        $em->flush();

        return $this->redirectToRoute('admin_address_index');
    }

}
