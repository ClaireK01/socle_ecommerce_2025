<?php

namespace App\Command;

use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:cart:remove-expired',
    description: 'Removes expired cart',
)]
class RemoveExpiredCartCommand extends Command
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em )
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void { }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = new \DateTime();
        $cartRepository = $this->em->getRepository(Cart::class);
        $carts = $cartRepository->getExpiredCart($date);

        foreach ($carts as $c){
            $this->em->remove($c);
        }

        $this->em->flush();

        return Command::SUCCESS;

    }

}
