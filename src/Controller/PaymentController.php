<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{

    const PUBLIC_KEY = "pk_test_51OUsGkFkgT4pe1YmpMYVsFnAOyWjsA5FH5VBoAFnVZ0DfvlPj8ME3GFFnDr2IoGrI9yxxFmryKABrHe6NcL03BzX00Zn9n8lXy";
    const PRIVATE_KEY = "sk_test_51OUsGkFkgT4pe1Ym524J1efunHtbuFqZsmXS3fE8mYdpaRaVILl1KM8eIXHeCovtdx8qEZobYb6wCchCszQFEP5u00R58jaibW";

    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {

        return $this->render('/app/payment:payment.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }
}
