<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Doctrine\ORM\QueryBuilder;

class ProductController extends AbstractController
{
    #[Route('{_locale}/categories/{id}/products/list', name: 'app_product_list', options: ["sitemap" => true, 'breadcrumb' => ['name' => '%name%', 'parent' => "app_category_list"]])]
    public function listAction(Category $category, Request $request, ProductRepository $productRepository,
                               PaginatorInterface $paginator, CartRepository $cartRepository): Response
    {
        $page = $request->get('page') ?? 1;
        $qb = $productRepository->createQueryBuilder("p");
        $qb ->where(":cat MEMBER OF p.categories")
            ->setParameter("cat", $category);

        $cart = $request->getSession()->get('cart') ? $cartRepository->find($request->getSession()->get('cart') ) : null;


        //->where('e.deletedAt is null')
        ;
        $pagination = $paginator->paginate(
            $qb,
            $page,
            10
        );

        return $this->render('app/product/list.html.twig', [
            'category' => $category,
            'pagination' => $pagination,
            'cart' => $cart
        ]);
    }

    #[Route('{_locale}/products/list/all', name: 'app_product_list_all', options: ["sitemap" => true, 'breadcrumb' => ['name' => '%name%', 'parent' => "app_category_list"]])]
    public function listAllAction(Request $request, ProductRepository $productRepository,
                               PaginatorInterface $paginator, CartRepository $cartRepository): Response
    {
        $page = $request->get('page') ?? 1;
        $qb = $productRepository->createQueryBuilder("p");
        $cart = $request->getSession()->get('cart') ? $cartRepository->find($request->getSession()->get('cart') ) : null;

        //->where('e.deletedAt is null')
        ;
        $pagination = $paginator->paginate(
            $qb,
            $page,
            10
        );

        return $this->render('app/product/list.html.twig', [
            'pagination' => $pagination,
            'cart' => $cart
        ]);
    }

    #[Route('{_locale}/products/{id}/show', name: 'app_product_show', options: ["sitemap" => true, 'breadcrumb' => ['name' => '%name%', 'parent' => 'app_product_list']])]
    public function showAction(Product $product, Request $request, ProductRepository $productRepository, CartRepository $cartRepository): Response
    {
        $cart = $request->getSession()->get('cart') ? $cartRepository->find($request->getSession()->get('cart') ) : null;
        $similars = $productRepository->findAll();

        return $this->render('app/product/show.html.twig', [
            'similarProducts' => $similars,
            'product' => $product,
            'cart' => $cart
        ]);
    }
}
