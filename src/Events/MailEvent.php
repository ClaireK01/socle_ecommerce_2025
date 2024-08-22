<?php

namespace App\Events;

use \App\Entity\User;


class MailEvent {

    const TYPES = ['client' => 1, 'admin' => 2, 'pro' => 3];
    private array $emails; // array [type => email] (ex : 1 => client@gmail.com, 3 => pro@gmail.com]

    private string $locale;

    private $entity;

    public function __construct($emails, $locale, $entity = null)
    {
        $this->emails = $emails;
        $this->locale = $locale;
        $this->entity = $entity;
    }


    public function getEmails(){
        return $this->emails;
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


}