<?php

namespace App\Command;

use App\Entity\AttributeItem;
use App\Entity\AttributeItemTranslation;
use App\Entity\Cart;
use App\Entity\Picture;
use App\Entity\Product;
use App\Entity\ProductAttribute;
use App\Entity\ProductAttributeTranslation;
use App\Entity\ProductTranslation;
use App\Service\EprolloService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:eprollo:update',
    description: 'Updates product inventory',
)]
class UpdateProductCommand extends Command
{
    private EntityManagerInterface $em;
    private EprolloService $eprolloService;
    public function __construct(EntityManagerInterface $em, EprolloService $eprolloService)
    {
        parent::__construct();
        $this->em = $em;
        $this->eprolloService = $eprolloService;
    }

    protected function configure(): void { }

    protected function updateProducts(InputInterface $input, OutputInterface $output): int
    {
        $products = $this->eprolloService->getProductList();

        foreach ($products as $productInfos){
            $product = new Product();
            $productTranslation = new ProductTranslation();
            $productTranslation->setTitle($productInfos['title']);
            $productTranslation->setLocale("en");

            $variantOptions = [];
            foreach ($productInfos['variantlist'] as $variant){
                $variantOptions[$variant['options1']][] = $variant;
            }

            foreach ($variantOptions as $i => $opt){
                $attribute = new ProductAttribute();
                $at = new ProductAttributeTranslation();
                $at->setTitle($i);
                foreach ($opt as $item){
                    $attributeItem = new AttributeItem();
                    $ait = new AttributeItemTranslation();
                    $ait->setTitle($item['options2']);
                    $attributeItem->setPrice($item['cost']);
                    $attributeItem->setProductAttribute($attribute);
                    $product->addAttributeItem($attributeItem);
                }
            }

            foreach ($productInfos['imagelist'] as $img){
                $pic = new Picture();
                $pic->setPath($img['src']);
                $product->addPicture($pic);
            }


            $this->em->persist($product);
//            $product->set
        }

        $this->em->flush();

        return Command::SUCCESS;
    }

}
