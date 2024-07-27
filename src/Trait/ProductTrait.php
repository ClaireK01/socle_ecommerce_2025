<?php

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;

trait ProductTrait
{

    #[ORM\Column(name: "price", type: "float", nullable: true)]
    private $price;

    #[ORM\Column(type: 'boolean', nullable:true)]
    private $online;

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
     * @return mixed
     */
    public function getOnline()
    {
        return $this->online;
    }

    /**
     * @param mixed $online
     */
    public function setOnline($online): void
    {
        $this->online = $online;
    }

}