<?php

namespace App\Events;

use \App\Entity\User;


class MailEvent {

    const TYPES = ['client' => 1, 'admin' => 2, 'pro' => 3];
    private string $locale;
    private ?string $filePath;

    private array $sendTo = [];

    private $entity;

    public function __construct($locale, $entity = null)
    {
        $this->locale = $locale;
        $this->entity = $entity;
    }

    public function getLocale(){
        return $this->locale;
    }

    public function getEntity(){
        return $this->entity;
    }

    public function getLabelType($type)
    {
        return array_search($type, self::TYPES);
    }

    public function addSendTo($type, $email){

        $this->sendTo[$type] = $email;

        return $this;
    }

    public function getSendTo(){
        return $this->sendTo;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
    }


}