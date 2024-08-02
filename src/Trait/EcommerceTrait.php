<?php

namespace App\Trait;

use App\Entity\CardProduct;
use App\Entity\EcommerceOrder;
use Doctrine\ORM\Mapping as ORM;

trait EcommerceTrait
{

    public function getCardProducts(){
        return $this->cardProducts;
    }

    public function addCardProduct(CardProduct $cardProduct): self
    {
        if (!$this->cardProducts->contains($cardProduct)) {
            $this->cardProducts->add($cardProduct);
            if($this instanceof EcommerceOrder){
                $cardProduct->setEcommerceOrder($this);
            }else{
                $cardProduct->setCart($this);
            }
        }

        return $this;
    }

    public function removeCardProduct(CardProduct $cardProduct): self
    {
        $this->cardProducts->removeElement($cardProduct);
        if($this instanceof EcommerceOrder){
            $cardProduct->setEcommerceOrder(null);
        }else{
            $cardProduct->setCart(null);
        }

        return $this;
    }

    public function setCardProducts($cardProducts)
    {
        $this->cardProducts = $cardProducts;

        return $this;
    }

    public function getTotalHt(){
        $total_ht = 0;
        foreach ($this->getCardProducts() as $c){
            /** @var CardProduct $c */
            $total_ht  += $c->getFinalPriceHt();
        }

        return $total_ht;
    }

    public function getTotalTtc(){
        $total_ttc = 0;
        foreach ($this->getCardProducts() as $c){
            /** @var CardProduct $c */
            $total_ttc  += $c->getFinalPriceTtc();
        }
        return $total_ttc;
    }

    public function getTotalUnitaireTtc(){
        $total = 0;
        foreach ($this->getCardProducts() as $c){
            /** @var CardProduct $c */
            $total  += $c->getPrice();
        }
        return $total;
    }

    public function getTotalUnitaireHt(){
        $total = 0;
        foreach ($this->getCardProducts() as $c){
            /** @var CardProduct $c */
            $total  += $c->getPriceHt();
        }
        return $total;
    }

    public function getTotalQuantity(){
        $total = 0;
        foreach ($this->getCardProducts() as $c){
            /** @var CardProduct $c */
            $total  += $c->getQuantity();
        }
        return $total;
    }

}