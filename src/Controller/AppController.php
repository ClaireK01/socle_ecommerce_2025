<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(Request $request): Response
    {
        if($this->getUser()){
            setcookie('user', $this->getUser()->getId());
        }

        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }


    #[Route('{_locale}/products/list', name: 'app_product_list')]
    public function list(Request $request, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('app/product/list.html.twig', [
            'products' => $products,
        ]);
    }
}
