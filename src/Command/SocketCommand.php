<?php

namespace App\Command;

use App\Entity\Chat;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

#[AsCommand(
    name: 'app:socket:run',
    description: 'Websocket PHP server',
)]
class SocketCommand extends Command
{

    private EncoderInterface $serializer;
    private LoggerInterface $logger;

    public function __construct(EncoderInterface $serializer, LoggerInterface $logger)
    {
        parent::__construct();
        $this->serializer = $serializer;
        $this->logger = $logger;

    }

    protected function configure(): void { }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->write('Serveur websocket en cours d\'utilisation');
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat($this->serializer)
                )
            ),
           8080, '127.0.0.1'
        );

        $server->run();

        return Command::SUCCESS;

    }

}
