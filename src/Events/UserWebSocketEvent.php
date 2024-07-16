<?php

namespace App\Events;

use \App\Entity\User;


class UserWebSocketEvent {

    private User $user;
    private ?string $resourceId;
    private ?string $message;

    public function __construct(User $user, $resourceId = null, $message = null){
        $this->user = $user;
        $this->resourceId = $resourceId;
        $this->message = $message;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function getResourceId()
    {
        return $this->resourceId;
    }

    public function getMessage()
    {
        return $this->message;
    }


}