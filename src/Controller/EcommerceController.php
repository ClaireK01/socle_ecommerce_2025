<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\CardProduct;
use App\Entity\Product;
use App\Form\AddressType;
use App\Form\UserType;
use App\Repository\AddressRepository;
use App\Repository\CardProductRepository;
use App\Repository\CartRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\ShippingRepository;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;


class EcommerceController extends AbstractController
{
    #[Route('{_locale}/products/{id}/add', name: 'add_to_cart', methods: 'POST')]
    public function addToCartAction(Product $product, Request $request,
                                    CardProductRepository $cardProductRepository, OrderService $orderService,
                                    CartRepository $cartRepository, EntityManagerInterface $entityManager)
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
            $orderService->addCardProductToCart($cart, $cardProduct);

        }else{
            $cardProduct = $results[array_key_first($results)];
            if($quantity < 1){
                $cart->removeCardProduct($cardProduct);
                $entityManager->remove($cardProduct);
                $entityManager->flush();
            }else{
                $cardProduct->setQuantity($quantity);
                $orderService->addCardProductToCart($cart, $cardProduct);
            }

        }


        return new JsonResponse(["cart" => $orderService->getCartInfos($cart)], 200);
    }

    #[Route('{_locale}/products/{id}/remove', name: 'remove_from_cart', methods: 'GET')]
    public function removeFromCart(CardProduct $cardProduct, Request $request, EntityManagerInterface $em, CartRepository $cartRepository)
    {
        $cart = $cartRepository->findOneBy(['session_id' => $request->getSession()->getId()]);

        foreach ($cart->getCardProducts() as $c){
            if($cardProduct == $c){
                $cart->removeCardProduct($c);
                break;
            }
        }

        $em->persist($cart);
        $em->flush();

        return $this->redirectToRoute('ecommerce_cart');
    }

    #[Route('{_locale}/cart', name: 'ecommerce_cart', methods: 'GET')]
    public function cartAction(Request $request, OrderService $orderService, CartRepository $cartRepository)
    {
        $cart = $cartRepository->findOneBy(['session_id' => $request->getSession()->getId()]);

        return $this->render('app/cart/show.html.twig', [
            'cart' => $cart,
        ]);
    }


    #[Route('{_locale}/cart/address', name: 'ecommerce_address', methods: ['GET', 'POST'])]
    public function cartAddressAction(Request $request, OrderService $orderService, CartRepository $cartRepository)
    {
        $user = $this->getUser();

        //code répété, mettre dans une func
        if($request->getMethod() == "POST"){
            $address = $request->request->get('address');
            $request->getSession()->set("address", $address);
            return $this->redirectToRoute('ecommerce_shipping');
        }

        return $this->render('app/cart/address.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('{_locale}/cart/address/add', name: 'ecommerce_address_add', methods: ['GET', 'POST'])]
    public function cartAddressAddAction(Request $request, EntityManagerInterface $entityManager,RouterInterface $router)
    {
        $user = $this->getUser();
        $address = new Address();

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->addAddress($address);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('ecommerce_address');
        }

        return $this->render('app/cart/form_addresses.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('{_locale}/cart/shipping', name: 'ecommerce_shipping', methods: ['GET', 'POST'])]
    public function cartShippingAction(Request $request, ShippingRepository $shippingRepository)
    {
        $user = $this->getUser();

        if(!$request->getSession()->get('address')){
            return $this->redirectToRoute('ecommerce_address');
        }

        $shippings = $shippingRepository->findAll();

        if($request->getMethod() == "POST"){
            $shipping = $request->request->get('shipping');
            $request->getSession()->set("shipping", $shipping);
            return $this->redirectToRoute('ecommerce_payment');
        }

        return $this->render('app/cart/shipping.html.twig', [
            'user' => $user,
            'shippings' => $shippings
        ]);
    }


    #[Route('{_locale}/cart/payment', name: 'ecommerce_payment', methods: ['GET', 'POST'])]
    public function cartPaymentAction(Request $request, PaymentMethodRepository $paymentMethodRepository)
    {
        $user = $this->getUser();

        if(!$request->getSession()->get('shipping')){
            return $this->redirectToRoute('ecommerce_shipping');
        }

        $paymentMethods = $paymentMethodRepository->findAll();


        if($request->getMethod() == "POST"){
            $payment = $request->request->get('payment');
            $request->getSession()->set("payment", $payment);
            return $this->redirectToRoute('ecommerce_summary');
        }

        return $this->render('app/cart/payment.html.twig', [
            'user' => $user,
            'paymentMethods' => $paymentMethods,
        ]);
    }


    #[Route('{_locale}/cart/summary', name: 'ecommerce_summary', methods: ['GET', 'POST'])]
    public function cartSummarytAction(Request $request, OrderService $orderService, CartRepository $cartRepository, ShippingRepository $shippingRepository,
                                       PaymentMethodRepository $paymentMethodRepository, AddressRepository $addressRepository)
    {

        if(!$addressId = $request->getSession()->get('address')){
            return $this->redirectToRoute('ecommerce_address');
        };

        if(!$shippingId = $request->getSession()->get('shipping')){
            return $this->redirectToRoute('ecommerce_shipping');
        };

        if(!$paymentId = $request->getSession()->get('payment')){
            return $this->redirectToRoute('ecommerce_payment');
        };

        $address = $addressRepository->find($addressId);
        $shipping = $shippingRepository->find($shippingId);
        $payment = $paymentMethodRepository->find($paymentId);

        $user = $this->getUser();
        $cart = $cartRepository->findOneBy(['session_id' => $request->getSession()->getId()]);


        return $this->render('app/cart/summary.html.twig', [
            'user' => $user,
            'cart' => $cart,
            'address' => $address,
            'shipping' => $shipping,
            'payment' => $payment,
        ]);
    }

    #[Route('{_locale}/cart/validate', name: 'ecommerce_validate', methods: ['GET', 'POST'])]
    public function validateCartAction(Request $request, OrderService $orderService, CartRepository $cartRepository, ShippingRepository $shippingRepository,
                                       PaymentMethodRepository $paymentMethodRepository, AddressRepository $addressRepository){

        $cart = $cartRepository->findOneBy(['session_id' => $request->getSession()->getId()]);


        if(!$addressId = $request->getSession()->get('address')){
            return $this->redirectToRoute('ecommerce_address');
        };

        if(!$shippingId = $request->getSession()->get('shipping')){
            return $this->redirectToRoute('ecommerce_shipping');
        };

        if(!$paymentId = $request->getSession()->get('payment')){
            return $this->redirectToRoute('ecommerce_payment');
        };

        $address = $addressRepository->find($addressId);
        $shipping = $shippingRepository->find($shippingId);
        $payment = $paymentMethodRepository->find($paymentId);

        $order = $orderService->createNewOrder($cart, $address, $shipping, $payment);

        if($order){
            return $this->redirectToRoute("app_stripe", ['number' => $order->getNumber()]);
        }

        return true ;

    }

    #[Route('{_locale}/cart/view', name: 'ecommerce_cart_view', methods: ['GET'])]
    public function cartViewAction(Request $request, CartRepository $cartRepository){

        $cart = $cartRepository->findOneBy(['session_id' => $request->getSession()->getId()]);

        $view =  $this->renderView('app/cart/cart_view.html.twig', [
            'cart' => $cart,
        ]);

        return new JsonResponse(['view' => $view]);
    }
}
