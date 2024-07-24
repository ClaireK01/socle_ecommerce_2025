<?php

namespace App\Entity;

use App\Repository\ShippingRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;



use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;


#[ORM\Entity(repositoryClass: ShippingRepository::class)]
class Shipping implements TranslatableInterface
{
    use SoftDeletableTrait,TimestampableTrait,TranslatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable:true)]
    private $type;


    #[ORM\Column(type: 'integer', nullable:true)]
    private $price;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
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