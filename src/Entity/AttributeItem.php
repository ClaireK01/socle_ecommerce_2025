<?php

namespace App\Entity;

use App\Repository\AttributeItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;



use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;


#[ORM\Entity(repositoryClass: AttributeItemRepository::class)]
class AttributeItem implements TranslatableInterface
{
    use TimestampableTrait,TranslatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(inversedBy: 'attributeItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductAttribute $productAttribute = null;

    #[ORM\Column(type: 'float', nullable:true)]
    private $price;

    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'attributeItems')]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function __toString() : string{
        return $this->translate()->getTitle();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductAttribute(): ?ProductAttribute
    {
        return $this->productAttribute;
    }

    public function setProductAttribute(?ProductAttribute $productAttribute): static
    {
        $this->productAttribute = $productAttribute;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->addAttributeItem($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            $product->removeAttributeItem($this);
        }

        return $this;
    }

}