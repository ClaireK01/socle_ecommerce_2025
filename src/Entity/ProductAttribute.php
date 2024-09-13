<?php

namespace App\Entity;

use App\Repository\ProductAttributeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;



use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;


#[ORM\Entity(repositoryClass: ProductAttributeRepository::class)]
class ProductAttribute implements TranslatableInterface
{
    use SoftDeletableTrait,TimestampableTrait,TranslatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToMany(mappedBy: 'productAttribute', targetEntity: AttributeItem::class, orphanRemoval: true)]
    private Collection $attributeItems;

    public function __construct()
    {
        $this->attributeItems = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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
            $attributeItem->setProductAttribute($this);
        }

        return $this;
    }

    public function removeAttributeItem(AttributeItem $attributeItem): static
    {
        if ($this->attributeItems->removeElement($attributeItem)) {
            // set the owning side to null (unless already changed)
            if ($attributeItem->getProductAttribute() === $this) {
                $attributeItem->setProductAttribute(null);
            }
        }

        return $this;
    }

}