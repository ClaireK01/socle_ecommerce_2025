<?php

namespace App\Trait;

use App\Entity\CardProduct;
use App\Entity\Constant;
use App\Entity\EcommerceOrder;
use Doctrine\ORM\Mapping as ORM;

trait EcommerceTrait
{

    public function getCardProducts(){
        return $this->cardProducts;
    }

    public function getFirstCardProduct(){
        if($this->cardProducts[0]){
            return $this->cardProducts[0];
        }

        return null;
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

    public function getTotalHt($withPromo = false){
        $total_ht = 0;

        if($withPromo){
            foreach ($this->getCardProducts() as $c){
                /** @var CardProduct $c */
                $total_ht  += $c->getFinalPriceHt(true);
            }
        }else{
            foreach ($this->getCardProducts() as $c){
                /** @var CardProduct $c */
                $total_ht  += $c->getFinalPriceHt();
            }
        }

        return $total_ht;
    }

    public function getTotalTtc($withPromo = false){
        $total_ttc = 0;

        if($withPromo){
            foreach ($this->getCardProducts() as $c){
                /** @var CardProduct $c */
                $total_ttc  += $c->getFinalPriceTtc(true);
            }
        }else{
            foreach ($this->getCardProducts() as $c){
                /** @var CardProduct $c */
                $total_ttc  += $c->getFinalPriceTtc();
            }
        }
        return $total_ttc;
    }

    public function getCartPromoAmount(){

        if($this->getPromoCode() && $this->getPromoCode()->getApplication() == Constant::PROMO_APPLICATION_CART){
            $code = $this->getPromoCode();
            return $code->getType() == Constant::PROMO_TYPE_PERCENTAGE ? ($code->getAmount() * $this->getTotalTtc() / 100 ) : $code->getAmount();
        }elseif ($this->getPromoCode() && $this->getPromoCode()->getApplication() == Constant::PROMO_APPLICATION_PRODUCT){
            return $this->getTotalTtc() - $this->getTotalTtc(true);
        }

        return 0;
    }

    public function getTotalUnitaireTtc($withPromo = false){
        $total = 0;

        if($withPromo){
            foreach ($this->getCardProducts() as $c){
                /** @var CardProduct $c */
                $total  += $c->getPrice(true);
            }
        }else{
            foreach ($this->getCardProducts() as $c){
                /** @var CardProduct $c */
                $total  += $c->getPrice();
            }
        }
        return $total;
    }

    public function getTotalUnitaireHt($withPromo = false){
        $total = 0;
        if($withPromo){
            foreach ($this->getCardProducts() as $c){
                /** @var CardProduct $c */
                $total  += $c->getPriceHt(true);
            }
        }else{
            foreach ($this->getCardProducts() as $c){
                /** @var CardProduct $c */
                $total  += $c->getPriceHt();
            }
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