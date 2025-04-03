<?php

namespace App\Entity;

use App\Repository\CardProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;




#[ORM\Entity(repositoryClass: CardProductRepository::class)]
class CardProduct
{
    use SoftDeletableTrait,TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    #[ORM\ManyToOne(targetEntity: Product::class)]
    private $product;

    #[ORM\Column(type: 'integer', nullable:true)]
    private $quantity;

    #[ORM\Column(type: 'float', nullable:true)]
    private $price;

    #[ORM\Column(type: 'float', nullable:true)]
    private $price_with_promo = 0;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: "cardProducts")]
    private $cart;

    #[ORM\ManyToOne(targetEntity: EcommerceOrder::class, inversedBy: "cardProducts")]
    private $ecommerceOrder;

    #[ORM\OneToMany(mappedBy: 'cardProduct', targetEntity: CardAttribute::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $cardAttributes;

    public function __construct(){
        $this->products = new ArrayCollection();
        $this->cardAttributes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProducts(){
        return $this->products;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getProduct() : ?Product
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product) : void
    {
        $this->product = $product;
    }

    /**
     * @return mixed
     */
    public function getPrice($withPromo = false)
    {
        if($withPromo){
            return $this->price_with_promo > 0 ? $this->price_with_promo : $this->price;
        }
        return $this->price;
    }

    public function getPriceHt($withPromo = false)
    {
        if($withPromo){
            return $this->getPrice(true)  - ( $this->product->getTva()->getRate() * $this->getPrice(true) / 100 );
        }
        return $this->getPrice()  - ( $this->product->getTva()->getRate() * $this->price / 100 );
    }

    public function getFinalPriceTtc($withPromo = false)
    {
        if($withPromo){
            return $this->getPrice(true) * $this->getQuantity();
        }

        return $this->getPrice() * $this->getQuantity();
    }

    public function getFinalPriceHt($withPromo = false)
    {
        if($withPromo) {
            return $this->getFinalPriceTtc(true) - ( $this->product->getTva()->getRate() * $this->getFinalPriceTtc(true) / 100 ) ;
        }
        return $this->getFinalPriceTtc() - ( $this->product->getTva()->getRate() * $this->getFinalPriceTtc() / 100 ) ;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param mixed $cart
     */
    public function setCart($cart): void
    {
        $this->cart = $cart;
    }

    /**
     * @return mixed
     */
    public function getEcommerceOrder()
    {
        return $this->ecommerceOrder;
    }

    /**
     * @param mixed $ecommerceOrder
     */
    public function setEcommerceOrder($ecommerceOrder): void
    {
        $this->ecommerceOrder = $ecommerceOrder;
    }

    /**
     * @return Collection<int, CardAttribute>
     */
    public function getCardAttributes(): Collection
    {
        return $this->cardAttributes;
    }

    public function addCardAttribute(CardAttribute $cardAttribute): static
    {
        if (!$this->cardAttributes->contains($cardAttribute)) {
            $this->cardAttributes->add($cardAttribute);
            $cardAttribute->setCardProduct($this);
        }

        return $this;
    }

    public function removeCardAttribute(CardAttribute $cardAttribute): static
    {
        if ($this->cardAttributes->removeElement($cardAttribute)) {
            // set the owning side to null (unless already changed)
            if ($cardAttribute->getCardProduct() === $this) {
                $cardAttribute->setCardProduct(null);
            }
        }

        return $this;
    }

    public function getFirstCardAttribute() : ?CardAttribute{
        if(count($this->cardAttributes) > 0){
            return $this->cardAttributes->first();
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getPriceWithPromo()
    {
        return $this->price_with_promo;
    }

    /**
     * @param mixed $price_with_promo
     */
    public function setPriceWithPromo($price_with_promo): void
    {
        $this->price_with_promo = $price_with_promo;
    }

}