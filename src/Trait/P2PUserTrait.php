<?php

namespace App\Trait;

use App\Entity\EcommerceOrder;
use Doctrine\ORM\Mapping as ORM;

trait P2PUserTrait
{

    #[ORM\OneToMany(mappedBy: "request_receiver", targetEntity: EcommerceOrder::class)]
    private $requests;

    public function getRequests(){
        return $this->requests;
    }

    public function addRequest($request): self
    {
        if (!$this->requests->contains($request)) {
            $this->requests->add($request);
        }

        return $this;
    }

    public function removeRequest($request): self
    {
        $this->requests->removeElement($request);

        return $this;
    }



}