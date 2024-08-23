<?php

namespace App\Trait;

use App\Entity\EcommerceOrder;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

trait P2PRequestTrait
{
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "requests")]
    public $request_receiver;

    /**
     * @return mixed
     */
    public function getRequestReceiver()
    {
        return $this->request_receiver;
    }

    /**
     * @param mixed $request_receiver
     */
    public function setRequestReceiver($request_receiver): void
    {
        $this->request_receiver = $request_receiver;
    }

}