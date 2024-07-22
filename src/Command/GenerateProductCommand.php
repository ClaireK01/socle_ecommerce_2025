<?php

namespace App\Command;

use App\Entity\Product;
use App\Entity\ProductTranslation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\VarDumper;

#[AsCommand(
    name: 'app:generate-product',
    description: 'Add a short description for your command',
)]
class GenerateProductCommand extends Command
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void { }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
       $product = new Product();
       $productTranslation = new ProductTranslation();
       $productTranslation->setTitle("Produit test");
       $productTranslation->setShortContent("Juste un produit test");
       $productTranslation->setContent("Juste un produit test");
       $productTranslation->setLocale('fr');

       $product->setPrice(15);
       $product->setOnline(true);
       $product->addTranslation($productTranslation);

       $this->em->persist($product);
       $this->em->persist($productTranslation);

       $this->em->flush();

        return Command::SUCCESS;

    }
}
