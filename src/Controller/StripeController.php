<?php

namespace App\Controller;

use App\Entity\EcommerceOrder;
use App\Repository\CartRepository;
use App\Repository\EcommerceOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeController extends AbstractController
{
    //clé test
    const API_KEY = 'sk_test_51OUsGkFkgT4pe1Ym524J1efunHtbuFqZsmXS3fE8mYdpaRaVILl1KM8eIXHeCovtdx8qEZobYb6wCchCszQFEP5u00R58jaibW';
    // test card  = Visa : 4242424242424242, Mastercard : 5555555555554444


    #[ParamConverter('number', class: EcommerceOrder::class)]
    #[Route('{number}/stripe', name: 'app_stripe')]
    public function index(EcommerceOrder $order): Response
    {
        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
            'order' => $order
        ]);
    }


    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request, EcommerceOrderRepository $orderRepository, EntityManagerInterface $entityManager, CartRepository $cartRepository)
    {
        $order = $request->request->get('order') ? $orderRepository->find($request->request->get('order')) : null ;
        $cart = $cartRepository->findOneBy(['session_id' => $request->getSession()->getId()]);

        if(!$order || $order->getStatus() != EcommerceOrder::PENDING){
            return $this->redirectToRoute('payment_stripe_cancel');
        }

        \Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);

        try{
            \Stripe\Charge::create ([
                "amount" => $order->getTotalPriceTtc() * 100,
                "currency" => "usd",
                "source" => $request->request->get('stripeToken'),
                "description" => "Binaryboxtuts Payment Test"
            ]);

            $order->setStatus(EcommerceOrder::PAYED);
            $entityManager->persist($order);
            $entityManager->remove($cart);
            $entityManager->flush();

            return $this->redirectToRoute('payment_stripe_success', [], Response::HTTP_SEE_OTHER);

        }catch (ApiErrorException $exception){
            $order->setStatus(EcommerceOrder::CANCELED);
            $entityManager->persist($order);
            $entityManager->flush();
            return $this->redirectToRoute('payment_stripe_cancel', [], Response::HTTP_SEE_OTHER);
        }

    }

    #[Route('payment/success', name: 'payment_stripe_success')]
    public function paymentSuccessAction(){


        return $this->render('app/payment/stripe_success.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('payment/cancel', name: 'payment_stripe_cancel')]
    public function paymentCancelAction(){

        return $this->render('app/payment/stripe_cancel.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }
}
