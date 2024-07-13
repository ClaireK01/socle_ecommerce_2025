<?php

namespace App\Command;

use App\Entity\Chat;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Ratchet\App;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Session\SessionProvider;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Twig\Environment;

#[AsCommand(
    name: 'app:socket:run',
    description: 'Websocket PHP server',
)]
class SocketCommand extends Command
{

    private EncoderInterface $serializer;
    private LoggerInterface $logger;
    private UserRepository $repository;
    private Environment $twig;

    public function __construct(EncoderInterface $serializer, LoggerInterface $logger,
                                UserRepository $repository, Environment $twig)
    {
        parent::__construct();
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->twig = $twig;

    }

    protected function configure(): void { }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->write('Serveur websocket en cours d\'utilisation');

        $memcache = new \Memcached();
        $memcache->addServer('127.0.0.1', 8000);

        $server = IoServer::factory(
            new HttpServer(
                new SessionProvider(
                    new WsServer(
                        new Chat($this->serializer, $this->logger, $this->repository,
                                $this->twig)
                    ),
                    new MemcachedSessionHandler($memcache)
                )
            ),
           8080, '127.0.0.1'
        );

        $server->run();

        return Command::SUCCESS;

    }

}
