<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use App\Trait\CategoryTrait;
use App\Trait\PictureTrait;
use App\Trait\ProductTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AssociationOverride;
use Doctrine\ORM\Mapping\AssociationOverrides;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;



use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;


#[AssociationOverrides([
    new AssociationOverride(
        name: "pictures",
        joinColumns: [new JoinColumn(name: "product_id")],
        inverseJoinColumns: [new JoinColumn(name: "picture_id")],
        joinTable: new JoinTable(
            name: "product_picture",
        )
    ),
    new AssociationOverride(
        name: "categories",
        joinColumns: [new JoinColumn(name: "product_id")],
        inverseJoinColumns: [new JoinColumn(name: "category_id")],
        joinTable: new JoinTable(
            name: "product_category",
        )
    )
])]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product implements TranslatableInterface, SoftDeletableInterface, TimestampableInterface
{
    use SoftDeletableTrait,TimestampableTrait,TranslatableTrait,
        ProductTrait, PictureTrait, CategoryTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Tva $tva = null;

    #[ORM\ManyToMany(targetEntity: AttributeItem::class, inversedBy: 'products', cascade: ['persist'])]
    private Collection $attributeItems;



    public function __construct(){
        $this->categories = new ArrayCollection();
        $this->attributeItems = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTva(): ?Tva
    {
        return $this->tva;
    }

    public function setTva(?Tva $tva): static
    {
        $this->tva = $tva;

        return $this;
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

    public function getVariantId(): string
    {
        return $this->variant_id;
    }

    public function setVariantId(string $variant_id): void
    {
        $this->variant_id = $variant_id;
    }

}