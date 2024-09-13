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

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: "cardProducts")]
    private $cart;

    #[ORM\ManyToOne(targetEntity: EcommerceOrder::class, inversedBy: "cardProducts")]
    private $ecommerceOrder;

    #[ORM\ManyToMany(targetEntity: AttributeItem::class)]
    private Collection $attributeItems;

    public function __construct(){
        $this->products = new ArrayCollection();
        $this->attributeItems = new ArrayCollection();
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
    public function getPrice()
    {
        return $this->price;
    }

    public function getPriceHt()
    {
        return $this->price  - ( $this->product->getTva()->getRate() * $this->price / 100 );
    }

    public function getFinalPriceTtc()
    {
        return $this->price * $this->getQuantity();
    }

    public function getFinalPriceHt()
    {
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
     * @return Collection<int, AttributeItem>
     */
    public function getAttributeItems(): Collection
    {
        return $this->attributeItems;
    }

    public function addAttributeItem(AttributeItem $attributeItem): static
    {
        if (!$this->attributeItems->contains($attributeItem)) {
            $this->attributeItems->add($attributeItem);
        }

        return $this;
    }

    public function removeAttributeItem(AttributeItem $attributeItem): static
    {
        $this->attributeItems->removeElement($attributeItem);

        return $this;
    }

}