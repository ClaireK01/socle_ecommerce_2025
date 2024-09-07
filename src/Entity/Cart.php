<?php

namespace App\Entity;

use App\Repository\CartRepository;
use App\Trait\EcommerceTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;




#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart implements TimestampableInterface
{
    use TimestampableTrait, EcommerceTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime', nullable:true)]
    private $expiration;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: CardProduct::class, cascade: ['persist'], orphanRemoval: false)]
    private $cardProducts;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?UserSession $user_session = null;


    public function __construct(){
        $this->cardProducts = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * @param mixed $expiration
     */
    public function setExpiration($expiration): void
    {
        $this->expiration = $expiration;
    }

    public function getUserSession(): ?UserSession
    {
        return $this->user_session;
    }

    public function setUserSession(?UserSession $user_session): static
    {
        $this->user_session = $user_session;

        return $this;
    }
}