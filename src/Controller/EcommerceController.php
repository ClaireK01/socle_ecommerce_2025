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
use App\Repository\ShippingRepository;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\VarDumper\VarDumper;

#[Route(requirements: ['_locale' => 'en|fr'])]
class EcommerceController extends AbstractController
{
    #[Route('{_locale}/cart/address/shipping', name: 'ecommerce_address', methods: ['GET', 'POST'])]
    public function cartShippingAddressAction(Request $request, AddressRepository $addressRepository, EntityManagerInterface $entityManager, OrderService $orderService)
    {
        $user = $this->getUser();
        $form = null;
        if(!$user){
            if($request->getSession()->get('address')){
                $addressShipping = $addressRepository->find($request->getSession()->get('address'));
                $addressShipping = $addressShipping ?? new Address();
                $orderService->removeAddress($request->getSession(), "address_facturation", $addressShipping);
            }else{
                $addressShipping = new Address();
            }

            $form = $this->createForm(AddressType::class, $addressShipping, ['guest' => true]);
            $form->handleRequest($request);
        }

        if($request->getMethod() == "POST"){
            if($user){
                $address = $request->request->get('address');
                $request->getSession()->set("address", $address);
                if($request->request->get('same_address') === "on") {
                    $request->getSession()->set("address_facturation", $address);
                }
                return $this->redirectToRoute('ecommerce_address_facturation');
            }else{
                if($form->isSubmitted() && $form->isValid()){

                    $entityManager->persist($addressShipping);
                    $entityManager->flush();
                    $request->getSession()->set("address", $addressShipping->getId());
                    if($request->request->get('same_address') === "on") {
                        $request->getSession()->set("address_facturation", $addressShipping->getId());
                    }
                    return $this->redirectToRoute('ecommerce_address_facturation');
                }
            }
        }

        return $this->render('app/cart/address.html.twig', [
            'user' => $user,
            'form' => $form?->createView(),
            'type' => "ship"
        ]);
    }

    #[Route('{_locale}/cart/address/facturation', name: 'ecommerce_address_facturation', methods: ['GET', 'POST'])]
    public function cartFacturationAddressAction(Request $request, EntityManagerInterface $entityManager, OrderService $orderService, CartRepository $cartRepository)
    {
        if($request->getSession()->get('address') == null){
            return $this->redirectToRoute('ecommerce_address');
        }

        if($request->getSession()->get('address_facturation') !== null){
            return $this->redirectToRoute('ecommerce_shipping');
        }

        $user = $this->getUser();
        $form = null;
        if(!$user){
            $addressFacturation = new Address();
            $form = $this->createForm(AddressType::class, $addressFacturation);
            $form->handleRequest($request);
        }

        //code répété, mettre dans une func
        if($request->getMethod() == "POST"){
            if($user){
                $address = $request->request->get('address');
                $request->getSession()->set("address_facturation", $address);
                return $this->redirectToRoute('ecommerce_shipping');

            }else{
                if($form->isSubmitted() && $form->isValid()){
                    $entityManager->persist($addressFacturation);
                    $entityManager->flush();
                    $request->getSession()->set("address_facturation", $addressFacturation->getId());
                    return $this->redirectToRoute('ecommerce_shipping');
                }
            }
        }

        return $this->render('app/cart/address.html.twig', [
            'user' => $user,
            'form' => $form?->createView(),
            'type' => "fact"
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

        if(!$addressFacId = $request->getSession()->get('address_facturation')){
            return $this->redirectToRoute('ecommerce_address_facturation');
        }

        if(!$shippingId = $request->getSession()->get('shipping')){
            return $this->redirectToRoute('ecommerce_shipping');
        };

        if(!$paymentId = $request->getSession()->get('payment')){
            return $this->redirectToRoute('ecommerce_payment');
        };

        $address = $addressRepository->find($addressId);
        $addressfac = $addressRepository->find($addressFacId);
        $shipping = $shippingRepository->find($shippingId);
        $payment = $paymentMethodRepository->find($paymentId);

        $user = $this->getUser();
        $cart = $request->getSession()->get('cart') ? $cartRepository->find($request->getSession()->get('cart') ) : null;

        if($cart && count($cart->getCardProducts()) == 0){
            return $this->redirectToRoute('ecommerce_cart');
        }


        return $this->render('app/cart/summary.html.twig', [
            'user' => $user,
            'cart' => $cart,
            'address' => $address,
            'addressfac' => $addressfac,
            'shipping' => $shipping,
            'payment' => $payment,
        ]);
    }

    #[Route('{_locale}/cart/validate', name: 'ecommerce_validate', methods: ['GET', 'POST'])]
    public function validateCartAction(Request $request, OrderService $orderService, CartRepository $cartRepository, ShippingRepository $shippingRepository,
                                       PaymentMethodRepository $paymentMethodRepository, AddressRepository $addressRepository){

        $cart = $request->getSession()->get('cart') ? $cartRepository->find($request->getSession()->get('cart') ) : null;


        if(!$addressId = $request->getSession()->get('address')){
            return $this->redirectToRoute('ecommerce_address');
        };

        if(!$addressFacId = $request->getSession()->get('address_facturation')){
            return $this->redirectToRoute('ecommerce_address');
        };

        if(!$shippingId = $request->getSession()->get('shipping')){
            return $this->redirectToRoute('ecommerce_shipping');
        };

        if(!$paymentId = $request->getSession()->get('payment')){
            return $this->redirectToRoute('ecommerce_payment');
        };

        $address = $addressRepository->find($addressId);
        $addressfac = $addressRepository->find($addressFacId);
        $shipping = $shippingRepository->find($shippingId);
        $payment = $paymentMethodRepository->find($paymentId);

        $order = $orderService->createNewOrder($cart, $address, $addressfac, $shipping, $payment);

        if($order){
            return $this->redirectToRoute("app_stripe", ['number' => $order->getNumber()]);
        }

        return true ;

    }

    #[Route('{_locale}/cart/view', name: 'ecommerce_cart_view', methods: ['GET'])]
    public function cartViewAction(Request $request, CartRepository $cartRepository){

        $cart = $request->getSession()->get('cart') ? $cartRepository->find($request->getSession()->get('cart') ) : null;

        $view =  $this->renderView('app/cart/cart_view.html.twig', [
            'cart' => $cart,
        ]);

        return new JsonResponse(['view' => $view]);
    }
}
