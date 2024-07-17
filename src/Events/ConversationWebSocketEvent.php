<?php

namespace App\Events;

use App\Entity\Conversation;
use App\Entity\User;


class ConversationWebSocketEvent {

    private Conversation $conversation;
    private User $user;
    private ?string $message;

    public function __construct(Conversation $conversation, User $user, $message = null){
        $this->conversation = $conversation;
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * @return Conversation
     */
    public function getConversation(): Conversation
    {
        return $this->conversation;
    }

    public function getMessage()
    {
        return $this->message;
    }


    public function getUser() : ?User
    {
        return $this->user;
    }

}