<?php


namespace App\Service;


use App\Entity\CardProduct;
use App\Entity\Cart;
use App\Entity\EcommerceOrder;
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

        $infos['total_quantity'] = array_reduce($cart->getCardProducts()->toArray(), function ($carry, $cp){
            return $cp->getQuantity() ? $carry + $cp->getQuantity() : $carry;
        });

        $infos['total'] = array_reduce($cart->getCardProducts()->toArray(), function ($carry, $cp){
            return $cp->getPrice() ? $carry + ( $cp->getPrice() * $cp->getQuantity() ) : $carry;
        });

        return $infos;

    }

    public function createNewOrder(Cart $cart, $address, $shipping, $payment){
        $total_ttc = 0;
        $total_ht = 0;


        foreach ($cart->getCardProducts() as $cp){
            /** @var CardProduct $cp */
            $total_ttc  += $cp->getPrice();
            $total_ht  += $cp->getPrice();
        }


        $order = new EcommerceOrder();

        foreach ($cart->getCardProducts() as $cp){
            $order->addCardProduct($cp);
        }
        $order->setTotalPriceTtc($total_ttc);
        $order->setTotalPriceHt($total_ht);
        $order->setStatus(EcommerceOrder::PENDING);
        $order->setShippingAddress($address);
        $order->setFacturationAddress($address);
        $order->setShipping($shipping);
        $order->setPaymentMethod($payment);

        $this->entityManager->persist($order);
        $this->entityManager->flush();


        return $order;
    }
}