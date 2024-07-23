<?php

namespace App\Controller;

use App\Entity\CardProduct;
use App\Entity\Product;
use App\Repository\CardProductRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

class EcommerceController extends AbstractController
{
    #[Route('{_locale}/products/list', name: 'app_product_list')]
    public function list(Request $request, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('app/product/list.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('{_locale}/products/{id}/add', name: 'add_to_cart', methods: 'POST')]
    public function addToCartAction(Product $product, Request $request, CardProductRepository $cardProductRepository, OrderService $orderService, CartRepository $cartRepository)
    {
        $session = $request->getSession();
        $quantity = json_decode($request->getContent())->quantity;
        $cart = $cartRepository->findOneBy(['session_id' => $session->getId()]);

        if(empty($cart)){
            $cart = $orderService->createNewCart($session);
        }
        $results = array_filter($cart->getCardProducts()->toArray(), function ($cp) use ($product, $quantity){
            /** @var CardProduct $cp  */
            return $cp->getProduct() == $product;
        });

        if(empty($results)){
            $cardProduct = new CardProduct();
            $cardProduct->setProduct($product);
            $cardProduct->setQuantity($quantity);
            $cardProduct->setPrice($product->getPrice());
        }else{
            $cardProduct = $results[array_key_first($results)];
            $newQuantity = $cardProduct->getQuantity() + $quantity;
            $cardProduct->setQuantity($newQuantity);
        }

        $orderService->addCardProductToCart($cart, $cardProduct);

        return new JsonResponse(["cart" => $orderService->getCartInfos($cart)], 200);
    }
}
