<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Psr\Http\Message\MessageInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;



use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\VarDumper\VarDumper;


//#[ORM\Entity(repositoryClass: ChatRepository::class)]
class Chat implements MessageComponentInterface
{
    protected $clients;
    private $serializer;
//    private $logger;

    public function __construct(EncoderInterface $serializer){
        $this->clients =  new \SplObjectStorage;
        $this->serializer = $serializer;
    }


    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        foreach($this->clients as $c){
            if($from !== $c){
                $c->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }



}