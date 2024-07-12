<?php

namespace App\Controller;

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


    #[Route('/stripe', name: 'app_stripe')]
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
        ]);
    }


    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request)
    {
        \Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        \Stripe\Charge::create ([
            "amount" => 5 * 100,
            "currency" => "usd",
            "source" => $request->request->get('stripeToken'),
            "description" => "Binaryboxtuts Payment Test"
        ]);
        $this->addFlash(
            'success',
            'Payment Successful!'
        );
        return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/payment/success', name: 'payment_stripe_success')]
    public function paymentSuccessAction(){

        return $this->render('app/payment/stripe_success.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/payment/cancel', name: 'payment_stripe_cancel')]
    public function paymentCancelAction(){

        return $this->render('app/payment/stripe_cancel.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }
}
