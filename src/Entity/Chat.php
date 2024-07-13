<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Twig\Environment;

class Chat implements MessageComponentInterface
{


    protected $clients;
    private $serializer;
    private $logger;
    private $repository;
    private $twig;

    public function __construct(EncoderInterface $serializer, LoggerInterface $logger,
                                UserRepository $repository, Environment $twig
    ){
        $this->clients =  new \SplObjectStorage;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->twig = $twig;
    }


    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $count = count($this->clients);
        $this->sendToClient($conn, $this->clients, $this->encode(['count' => $count]));
        $this->sendToClient($conn, $this->clients, $this->encode(['clients' => $this->getTemplate()]));

    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $this->sendToOtherClients($from, $this->clients, $this->encode(['message' => $msg]));

    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }

    public function sendToClient($from, $clients, $message){
        foreach($clients as $c){
            if($from == $c){
                $c->send($message);
            }
        }
    }

    public function sendToOtherClients($from, $clients, $message){
        foreach($clients as $c){
            if($from !== $c){
                $c->send($message);
            }
        }
    }

    public function encode($data){
        return $this->serializer->encode($data, 'json');
    }

    public function getTemplate(){
        return $this->twig->render('websocket/clients.html.twig', ["clients" => $this->clients]);
    }

}