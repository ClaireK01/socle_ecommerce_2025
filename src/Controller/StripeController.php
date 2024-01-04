<?php

namespace App\Controller;

use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeController extends AbstractController
{

    const PRIVATE_KEY = "sk_test_51OUsGkFkgT4pe1Ym524J1efunHtbuFqZsmXS3fE8mYdpaRaVILl1KM8eIXHeCovtdx8qEZobYb6wCchCszQFEP5u00R58jaibW";
    const PUBLIC_KEY = "pk_test_51OUsGkFkgT4pe1YmpMYVsFnAOyWjsA5FH5VBoAFnVZ0DfvlPj8ME3GFFnDr2IoGrI9yxxFmryKABrHe6NcL03BzX00Zn9n8lXy";
    //clé test
    const API_KEY = 'sk_test_51OUsGkFkgT4pe1Ym524J1efunHtbuFqZsmXS3fE8mYdpaRaVILl1KM8eIXHeCovtdx8qEZobYb6wCchCszQFEP5u00R58jaibW';

    #[Route('/payment/stripe/create', name: 'payment_stripe_create')]
    public function index(): Response
    {

        //si produit n'a pas jamais été acheté = création nouveau produit + enregistrement id en base, sinon redirect
        $stripe = new StripeClient("sk_test_VePHdqKTYQjKNInc7u56JBrQ");

        //creation produit
        $product = $stripe->products->create([
            'name' => 'Starter Subscription',
            'description' => '$12/Month subscription',
        ]);
        echo "Success! Here is your starter subscription product id : " . $product->id . "\n";

        //creation prix pr produit
        $price = $stripe->prices->create([
            'unit_amount' => 1200,
            'currency' => 'usd',
            'recurring' => ['interval' => 'month'],
            'product' => $product['id'],
        ]);
        echo "Success! Here is your starter subscription price id: " . $price->id . "\n";


        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/payment/stripe/form', name: 'payment_stripe_form')]
    public function paymentAction(): Response
    {
        $stripe = new StripeClient("sk_test_VePHdqKTYQjKNInc7u56JBrQ");
        \Stripe\Stripe::setApiKey(self::API_KEY);

        header('Content-Type: application/json');

        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price' => 'price_1OUsfxFkgT4pe1YmfZltxt10',
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('payment_stripe_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('payment_stripe_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);

        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
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
