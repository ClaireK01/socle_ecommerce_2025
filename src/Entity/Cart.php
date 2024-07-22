<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;




#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    use SoftDeletableTrait,TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', nullable:true)]
    private $session_id;

    #[JoinTable(name: "cart_card_product")]
    #[JoinColumn(name: "cart_id", referencedColumnName: "id")]
    #[InverseJoinColumn(name: "card_product_id", referencedColumnName: "id")]
    private $cardProducts;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCardProducts(){
        return $this->cardProducts;
    }

    public function addCardProduct(CardProduct $cardProduct): self
    {
        if (!$this->cardProducts->contains($cardProduct)) {
            $this->cardProducts->add($cardProduct);
        }

        return $this;
    }

    public function removeCardProduct(CardProduct $cardProduct): self
    {
        $this->cardProducts->removeElement($cardProduct);

        return $this;
    }
}