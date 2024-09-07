<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('{_locale}/categories/list', name: 'app_category_list', options: ["sitemap" => true, 'breadcrumb' => ['name' => 'category.list']])]
    public function listAction(Request $request, CategoryRepository $categoryRepository,
                               PaginatorInterface $paginator, CartRepository $cartRepository): Response
    {
        $page = $request->get('page') ?? 1;
        $qb = $categoryRepository->createQueryBuilder("p");
        $cart = $request->getSession()->get('cart') ? $cartRepository->find($request->getSession()->get('cart') ) : null;

        //->where('e.deletedAt is null')
        ;
        $pagination = $paginator->paginate(
            $qb,
            $page,
            10
        );

        return $this->render('app/category/list.html.twig', [
            'pagination' => $pagination,
            'cart' => $cart
        ]);
    }

//    #[Route('{_locale}/categories/{id}/show', name: 'app_category_show')]
//    public function showAction(Category $category, Request $request, CategoryRepository $categoryRepository, CartRepository $cartRepository): Response
//    {
//                $cart = $request->getSession()->get('cart') ? $cartRepository->find($request->getSession()->get('cart') ) : null;
//        $similars = $categoryRepository->findAll();
//
//        return $this->render('app/category/show.html.twig', [
//            'similarCategories' => $similars,
//            'category' => $category,
//            'cart' => $cart
//        ]);
//    }
}
