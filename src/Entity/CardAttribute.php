<?php

namespace App\Entity;

use App\Repository\CardAttributeRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;



use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;


#[ORM\Entity(repositoryClass: CardAttributeRepository::class)]
class CardAttribute implements TranslatableInterface
{
    use SoftDeletableTrait,TimestampableTrait,TranslatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(inversedBy: 'cardAttributes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CardProduct $cardProduct = null;

    #[ORM\Column(type: 'float', nullable:true)]
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCardProduct(): ?CardProduct
    {
        return $this->cardProduct;
    }

    public function setCardProduct(?CardProduct $cardProduct): static
    {
        $this->cardProduct = $cardProduct;

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

}