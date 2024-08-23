<?php

namespace App\Events;

use App\Entity\EcommerceOrder;
use \App\Entity\User;


class EcommerceOrderEvent {

//    private User $user;
    private EcommerceOrder $order;
    private $state;
    private $locale;

    public function __construct($order, $state, $locale){
//        $this->user = $user;
        $this->order = $order;
        $this->state = $state;
        $this->locale = $locale;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function getOrder(){
        return $this->order;
    }

    public function getState(){
        return $this->state;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }


}