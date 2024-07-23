<?php


namespace App\Service;


use App\Entity\CardProduct;
use App\Entity\Cart;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\VarDumper\VarDumper;

class OrderService
{
    private CartRepository $cartRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, CartRepository $cartRepository)
    {
        $this->entityManager = $entityManager;
        $this->cartRepository = $cartRepository;
    }

    public function createNewCart(SessionInterface $session){
        $cart = new Cart();
        $cart->setSessionId($session->getId());

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return $cart;
    }

    public function addCardProductToCart(Cart $cart, CardProduct $cartProduct){
        $cart->addCardProduct($cartProduct);

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return true;
    }

    public function getCartInfos(Cart $cart){

        $infos = [];


        $infos['total_quantity'] = array_reduce($cart->getCardProducts()->toArray(), function ($carry = 0, $cp){
//            die(VarDumper::dump($cp));
            return $cp->getQuantity() ?? 0;
        });

        $infos['total'] = array_reduce($cart->getCardProducts()->toArray(), function ($carry = 0, $cp){
            return $cp->getPrice() ? $cp->getPrice() * $cp->getQuantity() : 0;
        });

        return $infos;

    }
}