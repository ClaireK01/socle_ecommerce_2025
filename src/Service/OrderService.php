<?php


namespace App\Service;


use App\Entity\Address;
use App\Entity\AttributeItem;
use App\Entity\CardAttribute;
use App\Entity\CardAttributeTranslation;
use App\Entity\CardProduct;
use App\Entity\Cart;
use App\Entity\EcommerceOrder;
use App\Entity\User;
use App\Entity\UserSession;
use App\Repository\AttributeItemRepository;
use App\Repository\CartRepository;
use App\Repository\EcommerceOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\VarDumper\VarDumper;

class OrderService
{
    private CartRepository $cartRepository;
    private EntityManagerInterface $entityManager;
    private EcommerceOrderRepository $ecommerceOrderRepository;
    private  AttributeItemRepository $attributeItemRepository;

    public function __construct(EntityManagerInterface $entityManager, CartRepository $cartRepository, EcommerceOrderRepository $ecommerceOrderRepository, AttributeItemRepository $attributeItemRepository)
    {
        $this->entityManager = $entityManager;
        $this->cartRepository = $cartRepository;
        $this->ecommerceOrderRepository = $ecommerceOrderRepository;
        $this->attributeItemRepository = $attributeItemRepository;
    }

    public function createNewCart(SessionInterface $session, $user = null){
        $date = new \DateTime();
        $expire = $date->modify('+ 3 days');
        $cart = new Cart();
        $cart->setExpiration($expire);
        if($user instanceof User){
            $userSession = new UserSession();
            $userSession->setUser($user);
            $userSession->setSessionId($session->getId());
            $cart->setUserSession($userSession);
        }
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        $session->set("cart", $cart->getId());

        return $cart;
    }

    public function addCardProductToCart(Cart $cart, CardProduct $cartProduct){
        $cart->addCardProduct($cartProduct);

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return true;
    }

    public function addCardAttributesToCardProduct(CardProduct $cardProduct, array $attributes){

        foreach ($attributes as $a){
            $item = $this->attributeItemRepository->find($a);
            if($item){
                /** @var AttributeItem $item */
                $ca = new CardAttribute();
                $ca->setPrice($item->getPrice());
                $ca->setRef($item->getId());
                foreach ($item->getTranslations() as $t){
                    $newTranslation = new CardAttributeTranslation($t->getTitle(), $t->getContent(), $t->getShortContent(), $item->getProductAttribute()->translate($t->getLocale())->getTitle(), $t->getLocale());
                    $ca->addTranslation($newTranslation);
                }
                $cardProduct->addCardAttribute($ca);
            }
        }

        $ca_price = array_reduce($cardProduct->getCardAttributes()->toArray(), function ($carry, $ca){
            return $ca->getPrice() ? $carry + $ca->getPrice() : $carry;
        });
        $cardProduct->setPrice($cardProduct->getPrice() + $ca_price);

        $this->entityManager->persist($cardProduct);
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

        $order = new EcommerceOrder();

        foreach ($cart->getCardProducts() as $cp){
            $order->addCardProduct($cp);
        }
        $order->setTotalPriceTtc($cart->getTotalTtc());
        $order->setTotalPriceHt($cart->getTotalHt());
        $order->setStatus(EcommerceOrder::PENDING);
        $order->setShippingAddress($address);
        $order->setFacturationAddress($address);
        $order->setShipping($shipping);
        $order->setPaymentMethod($payment);

        $order = $this->setOrderAdresses($order, $address, $address);

        $order->setNumber($this->ecommerceOrderRepository->getLastOrderNumber() + 1);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    public function removeCart(Cart $cart)
    {
        foreach ($cart->getCardProducts() as $c){
            $c->setCart(null);
            $this->entityManager->persist($c);
        }
        $this->entityManager->remove($cart);
        $this->entityManager->flush();
    }

    public function destroySession($session)
    {
        $this->removeAddress($session, "address_facturation");
        $this->removeAddress($session, "address");
        $session->remove('cart_id');
        $session->remove('shopCount');
        $session->remove('address');
        $session->remove('address_facturation');
    }

    public function removeAddress($session, $key)
    {
        $address = $session->get($key) ? $this->entityManager->getRepository(Address::class)->find($session->get('address')) : null;
        if($address && !$address->getUser()){
            $this->entityManager->remove($address);
        }
        $this->entityManager->flush();
    }

    public function checkSameProduct(CardProduct $cardProduct,  $attributes, $newProduct)
    {
        $same = true;
        $cardAttrs = array_map(function($e){ return $e->getRef(); }, $cardProduct->getCardAttributes()->toArray());

        foreach ($attributes as $a){
            if(!in_array($a, $cardAttrs) && $a != ""){
                $same = false;
            }
        }

        foreach ($cardAttrs as $a){
            if(!in_array($a, $attributes)){
                $same = false;
            }
        }

        return $same && $newProduct == $cardProduct->getProduct();
    }

    public function setOrderAdresses(EcommerceOrder $order,
                                     Address $shippingAddress,
                                     Address $facturationAddress ){

        $order->setShippingFirstName($shippingAddress->getFirstName());
        $order->setShippingLastName($shippingAddress->getLastName());
        $order->setShippingAddressLine($shippingAddress->getAddress());
        $order->setShippingCode($shippingAddress->getCode());
        $order->setShippingCity($shippingAddress->getCity());
        $order->setShippingInfos($shippingAddress->getInfos());

        $order->setFacturationFirstName($facturationAddress->getFirstName());
        $order->setFacturationLastName($facturationAddress->getLastName());
        $order->setFacturationAddressLine($facturationAddress->getAddress());
        $order->setFacturationCode($facturationAddress->getCode());
        $order->setFacturationCity($facturationAddress->getCity());
        $order->setFacturationInfos($facturationAddress->getInfos());

        $order->setPhone($shippingAddress->getPhone());
        $order->setEmail($shippingAddress->getUser() ? $shippingAddress->getUser()->getEmail() : $shippingAddress->getEmail());

        return $order;
    }
}