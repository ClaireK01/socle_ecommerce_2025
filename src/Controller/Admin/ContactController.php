<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\AdminService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/contact')]
class ContactController extends AbstractController
{
    #[Route('/', name: 'admin_contact_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator, AdminService $adminService): Response
    {

        $properties = [
            'firstName',
            'lastName'
        ];
        $filter = $request->request->all();
        $page = $request->get('page') ?? 1;

        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(Contact::class, 'e')
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
            'class' => Contact::class
        ]);
    }
//
//    #[Route('/{id}/edit', name: 'admin_contact_edit')]
//    #[Route('/new', name: 'admin_contact_new')]
//    public function editAction(Request $request, EntityManagerInterface $em, $id = null): Response
//    {
//        $contact = $id != null && $em->getRepository(Contact::class)->find($id) ? $em->getRepository(Contact::class)->find($id) : new Contact();
//
//        $form = $this->createForm(ContactType::class, $contact);
//
//        $form->handleRequest($request);
//
//        if($form->isSubmitted() && $form->isValid()){
//            $em->persist($contact);
//            $em->flush();
//            return $this->redirectToRoute('admin_contact_index');
//        }
//        return $this->render('/admin/edit.html.twig', [
//            'contact' => $contact,
//            'form' => $form->createView()
//        ]);
//    }

    #[ParamConverter('contact', class: Contact::class)]
    #[Route('/{id}/delete', name: 'admin_contact_delete')]
    public function deleteAction(Contact $contact, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($contact);
        $em->flush();

        return $this->redirectToRoute('admin_contact_index');
    }

    #[ParamConverter('contact', class: Contact::class)]
    #[Route('/{id}/show', name: 'admin_contact_show')]
    public function showAction(Contact $contact, Request $request, EntityManagerInterface $em): Response
    {
        return $this->render('/admin/show.html.twig', [
            'entity' => $contact,
        ]);
    }

}
