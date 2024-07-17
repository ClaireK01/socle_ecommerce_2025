<?php

namespace App\Events;

use \App\Entity\User;


class UserWebSocketEvent {

    private User $user;
    private ?string $resourceId;

    public function __construct(User $user, $resourceId = null){
        $this->user = $user;
        $this->resourceId = $resourceId;}

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


}