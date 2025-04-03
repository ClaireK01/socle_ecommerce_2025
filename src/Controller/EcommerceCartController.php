<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\AttributeItem;
use App\Entity\CardProduct;
use App\Entity\Product;
use App\Form\AddressType;
use App\Form\UserType;
use App\Repository\AddressRepository;
use App\Repository\CardProductRepository;
use App\Repository\CartRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\PromoCodeRepository;
use App\Repository\ShippingRepository;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(requirements: ['_locale' => 'en|fr'])]
class EcommerceCartController extends AbstractController
{

    #codepromo
    #[Route('{_locale}/cart/promo', name: 'cart_add_promo', methods: 'POST')]
    public function cartCodePromoAction(Request $request, OrderService $orderService,
                                        CartRepository $cartRepository, PromoCodeRepository $promoCodeRepository, TranslatorInterface $translator)
    {
        $cart = $request->getSession()->get('cart') ? $cartRepository->find($request->getSession()->get('cart') ) : null;
        $code = json_decode($request->getContent())->promo;

        //refractorer
        if(!$cart) {
            $message = $translator->trans("promo.empty", [], 'messages', $request->getLocale());
            $view = $this->renderView('app/cart/cart_view.html.twig', ['cart' => $cart, "message" => $message]);
            return new JsonResponse(["view" => $view, 'message' => $message, "code" => 201], 201);
        }

        if (!$code) {
            $message = $translator->trans("promo.empty", [], 'messages', $request->getLocale());
            $view = $this->renderView('app/cart/cart_view.html.twig', ['cart' => $cart, "message" => $message]);
            return new JsonResponse(["view" => $view, 'message' => $message, "code" => 201], 201);
        }

        $promoCode = $promoCodeRepository->findOneBy(['code' => $code]);
        if (!$promoCode) {
            $message = $translator->trans("promo.invalid", [], 'messages', $request->getLocale());
            $view = $this->renderView('app/cart/cart_view.html.twig', ['cart' => $cart, "message" => $message]);
            return new JsonResponse(["view" => $view, 'message' => $message, "code" => 201], 201);
        }

        if ($promoCode->getExpiringDate() < new \DateTime()) {
            $message = $translator->trans("promo.expired", ["amount" => $promoCode->getMinAmount() - $cart->getTotalTtc()], 'messages', $request->getLocale());
            $view = $this->renderView('app/cart/cart_view.html.twig', ['cart' => $cart, "message" => $message]);
            return new JsonResponse(["view" => $view, 'message' => $message, "code" => 201], 201);
        }

        if ($cart->getTotalTtc() < $promoCode->getMinAmount()) {
            $message = $translator->trans("promo.minamount", ["amount" => $promoCode->getMinAmount() - $cart->getTotalTtc()], 'messages', $request->getLocale());
            $view = $this->renderView('app/cart/cart_view.html.twig', ['cart' => $cart, "message" => $message]);
            return new JsonResponse(["view" => $view, 'message' => $message, "code" => 201], 201);
        }

        $cart = $orderService->addPromoCode($cart, $promoCode);
        $message = $promoCode->getMessage();
        $view = $this->renderView('app/cart/cart_view.html.twig', ['cart' => $cart, "message" => $message]);

        return new JsonResponse(["view" => $view, 'message' => $message, "code" => 200], 200);
    }

    #[Route('{_locale}/cart/preview', name: 'cart_preview', methods: 'GET')]
    public function cartPreviewAction(Request $request,
                                    CardProductRepository $cardProductRepository, OrderService $orderService,
                                    CartRepository $cartRepository, EntityManagerInterface $entityManager)
    {

        $cart = $request->getSession()->get('cart') ? $cartRepository->find($request->getSession()->get('cart') ) : null;
        $view = $this->renderView('app/cart/cart_preview.html.twig', ['cart' => $cart]);

        return new JsonResponse(["view" => $view], 200);
    }

    #[Route('{_locale}/products/{id}/add', name: 'add_to_cart', methods: 'POST')]
    public function addToCartAction(Product $product, Request $request,
                                    CardProductRepository $cardProductRepository, OrderService $orderService,
                                    CartRepository $cartRepository, EntityManagerInterface $entityManager)
    {
        $session = $request->getSession();
        $quantity = json_decode($request->getContent())->quantity;
        $attributes = explode("|", json_decode($request->getContent())->attributes);
        $cart = null;
        if($cartId = $session->get('cart')){
            $cart = $cartRepository->find($cartId);
        }
        if(!$cart){
            $cart = $orderService->createNewCart($session, $this->getUser());
        }

        $results = array_filter($cart->getCardProducts()->toArray(), function ($cp) use ($orderService, $attributes, $product){
            return $orderService->checkSameProduct($cp, $attributes, $product);
        });

        if(empty($results)){
            $cardProduct = new CardProduct();
            $cardProduct->setProduct($product);
            $cardProduct->setQuantity($quantity);
            $cardProduct->setPrice($product->getPrice());
            if(!empty($attributes)){
                $orderService->addCardAttributesToCardProduct($cardProduct, $attributes);
            }
            $orderService->addCardProductToCart($cart, $cardProduct);
        }else{
            $cardProduct = $results[array_key_first($results)];
            if($quantity < 1){
                $cart->removeCardProduct($cardProduct);
                $entityManager->remove($cardProduct);
                $entityManager->flush();
            }else{
                $cardProduct->setQuantity($cardProduct->getQuantity() + $quantity);
                $orderService->addCardProductToCart($cart, $cardProduct);
            }
        }

        $cartInfos = $orderService->getCartInfos($cart);
        $view = $this->renderView('app/cart/cart_preview.html.twig', ['cart' => $cart]);
        $session->set('shopCount', $cartInfos['total_quantity']);

        return new JsonResponse(["cart" => $cartInfos, 'cartview' => $view], 200);
    }

    #[Route('{_locale}/cart/{id}/quantity', name: 'update_quantity', methods: 'POST')]
    public function updateQuantityFromCartAction(CardProduct $cardProduct, Request $request, OrderService $orderService,
                                                 CartRepository $cartRepository, EntityManagerInterface $entityManager)
    {
        $session = $request->getSession();
        $quantity = json_decode($request->getContent())->quantity;
        if($cartId = $session->get('cart')){
            $cart = $cartRepository->find($cartId);
        }

        if($quantity < 1){
            $cart->removeCardProduct($cardProduct);
            $entityManager->remove($cardProduct);
        }else{
            $cardProduct->setQuantity($quantity);
            $entityManager->persist($cardProduct);
        }

        $entityManager->flush();

        $cartInfos = $orderService->getCartInfos($cart);
        $cartView = $this->renderView('app/cart/cart_preview.html.twig', ['cart' => $cart]);
        $session->set('shopCount', $cartInfos['total_quantity']);

        return new JsonResponse(["cart" => $cartInfos, 'cartview' => $cartView], 200);
    }

    #[Route('{_locale}/cart/{id}/attributes', name: 'product_price_attribute', methods: 'POST')]
    public function getPriceProductAttributes(Product $product, Request $request, CartRepository $cartRepository, EntityManagerInterface $entityManager)
    {
        $attributes = explode("|", json_decode($request->getContent())->attributes);
        $price = array_reduce($product->getAttributeItems()->toArray(), function ($carry, $attr) use ($attributes){
            /** @var AttributeItem $attr */
            return in_array($attr->getId(), $attributes) ? $carry  + $attr->getPrice() : $carry;
        });

        return new JsonResponse(["total" => $price + $product->getPrice()], 200);

    }

    #[Route('{_locale}/products/{id}/remove', name: 'remove_from_cart', methods: 'GET')]
    public function removeFromCart(CardProduct $cardProduct, Request $request, EntityManagerInterface $em,
                                   CartRepository $cartRepository, OrderService $orderService)
    {
        $cart = $request->getSession()->get('cart') ? $cartRepository->find($request->getSession()->get('cart') ) : null;

        if($cart){
            foreach ($cart->getCardProducts() as $c){
                if($cardProduct == $c){
                    $cart->removeCardProduct($c);
                    $em->remove($cardProduct);
                    break;
                }
            }
            $em->persist($cart);
            $em->flush();
            $cartView = $this->renderView('app/cart/cart_preview.html.twig', ['cart' => $cart]);
            $cartInfos = $orderService->getCartInfos($cart);
            $request->getSession()->set('shopCount', $cartInfos['total_quantity']);

            if($request->isXmlHttpRequest()){
                return new JsonResponse(['cartview' => $cartView, 'cart' => $cartInfos, 'shopcount' => $cartInfos['total_quantity'] ?? 0]);
            }
        }

        return $this->redirectToRoute('ecommerce_cart');
    }

    #[Route('{_locale}/cart', name: 'ecommerce_cart', options: ['breadcrumb' => ['name' => 'cart']], methods: 'GET')]
    public function cartAction(Request $request, OrderService $orderService, CartRepository $cartRepository)
    {
        $cart = $request->getSession()->get('cart') ? $cartRepository->find($request->getSession()->get('cart') ) : null;

        return $this->render('app/cart/show.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('{_locale}/cart/login', name: 'ecommerce_cart_login', options: ['breadcrumb' => ['name' => 'cart']], methods: 'GET')]
    public function cartLoginAction(Request $request)
    {
        return $this->render('app/cart/cart_login.html.twig', []);
    }
}
