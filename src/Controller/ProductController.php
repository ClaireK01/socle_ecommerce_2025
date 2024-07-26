<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('{_locale}/products/list', name: 'app_product_list')]
    public function listAction(Request $request, ProductRepository $productRepository, PaginatorInterface $paginator): Response
    {
        $page = $request->get('page') ?? 1;
        $qb = $productRepository->createQueryBuilder("p");
            //->where('e.deletedAt is null')
        ;
        $pagination = $paginator->paginate(
            $qb,
            $page,
            10
        );

        return $this->render('app/product/list.html.twig', [
            'products' => $pagination,
        ]);
    }

    #[Route('{_locale}/products/{id}/show', name: 'app_product_show')]
    public function showAction(Product $product, Request $request, ProductRepository $productRepository): Response
    {
        $similars = $productRepository->findAll();

        return $this->render('app/product/show.html.twig', [
            'similarProducts' => $similars,
            'product' => $product,
        ]);
    }
}
