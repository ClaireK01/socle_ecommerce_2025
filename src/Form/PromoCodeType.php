<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\PromoCode;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromoCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Pourcentage'=>1,
                    'Montant'=> 2,
                ],
                "attr" => [
                    'id' => "tom-select-type"
                ],
                'autocomplete' => true
            ])
            ->add('application', ChoiceType::class, [
                'choices' => [
                    'Produit'=>1,
                    'Panier'=> 2,
                ],
                "attr" => [
                    'id' => "tom-select-application"
                ],
                'autocomplete' => true
            ])
            ->add('code')
            ->add('amount')
            ->add('message')
            ->add('expiringDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('products', EntityType::class, [
                'required' => false,
                'class' => Product::class,
                "placeholder" => "Produits",
                "query_builder" => function (ProductRepository $r) {
                    return $r->createQueryBuilder('p');
                },
                "autocomplete" => true,
                'multiple' => true,
                "attr" => [
                    'id' => "tom-select-promo"
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PromoCode::class,
        ]);
    }
}
