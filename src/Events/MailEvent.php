<?php

namespace App\Events;

use \App\Entity\User;


class MailEvent {

    const TYPES = ['client' => 1, 'admin' => 2, 'pro' => 3];

    private User $user;
    private string $type;

    public function __construct(User $user, $type)
    {
        $this->user = $user;
        $this->type = $type;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function getType(){
        return $this->type;
    }


}