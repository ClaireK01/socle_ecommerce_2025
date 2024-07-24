<?php

namespace App\Command;

use App\Entity\Address;
use App\Entity\PaymentMethod;
use App\Entity\PaymentMethodTranslation;
use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Entity\Shipping;
use App\Entity\ShippingTranslation;
use App\Entity\User;
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
    name: 'app:generate-fixture',
    description: 'Add a short description for your command',
)]
class GenerateFixtureCommand extends Command
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

//        $this->generateProduct();
        $this->generateAdress(6);
//        $this->generatePaymentMethod();
//        $this->generateShipping();

        return Command::SUCCESS;

    }

    public function generateProduct(){

        for ($i = 0; $i <= 3; $i++){
            $entity = new Product();
            $entityTranslation = new ProductTranslation();
            $entityTranslation->setTitle("Produit test " . $i+1 );
            $entityTranslation->setShortContent("Juste un produit test");
            $entityTranslation->setContent("Juste un produit test");
            $entityTranslation->setLocale('fr');

            $entity->setPrice(15);
            $entity->setOnline(true);
            $entity->addTranslation($entityTranslation);

            $this->em->persist($entity);
            $this->em->persist($entityTranslation);
        }


        $this->em->flush();

    }

    public function generateAdress($id)
    {
        $user = $this->em->getRepository(User::class)->find($id);

        $entity = new Address();
        $entity->setAddress("50 rue du test");
        $entity->setZipCode("69100");
        $entity->setCity("Villeurbanne");
        $entity->setLabel("Chez moi 2");
        $entity->setUser($user);

        $this->em->persist($entity);
        $this->em->flush($entity);
    }

    public function generatePaymentMethod()
    {

        $entity = new PaymentMethod();
        $entityTranslation = new PaymentMethodTranslation();

        $entityTranslation->setTitle('test payment');
        $entityTranslation->setContent('paiment rapide');
        $entityTranslation->setShortContent('paiment rapide');
        $entityTranslation->setLocale('fr');
        $entity->addTranslation($entityTranslation);

        $this->em->persist($entity);
        $this->em->flush($entity);
    }


    public function generateShipping()
    {

        $entity = new Shipping();
        $entityTranslation = new ShippingTranslation();

        $entity->setPrice(2);
        $entityTranslation->setTitle('test shipping');
        $entityTranslation->setContent('shipping gratuit');
        $entityTranslation->setShortContent('shipping gratuit');
        $entityTranslation->setLocale('fr');
        $entity->addTranslation($entityTranslation);

        $this->em->persist($entity);
        $this->em->flush($entity);
    }
}
