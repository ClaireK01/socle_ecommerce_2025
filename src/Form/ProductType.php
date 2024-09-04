<?php

namespace App\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Tva;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'title' => [
                        'field_type' => TextType::class,
                    ],
                    'shortContent' => [
                      'field_type' => TextType::class,
                    ],
                    'content' => [
                        'field_type' => TextareaType::class,
                    ]
                ]
            ])
            ->add('tva', EntityType::class, [
                'class' => Tva::class,
                "placeholder" => "TVA",
                "autocomplete" => true,
                "attr" => [
                    'id' => "tom-select-tva"
                ]
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                "placeholder" => "Categories",
                "query_builder" => function (CategoryRepository $r) {
                    return $r->createQueryBuilder('c');
                },
                "autocomplete" => true,
                'multiple' => true,
                "attr" => [
                    'id' => "tom-select-category"
                ]
            ])
            ->add('price')
            ->add('pictures', CollectionType::class, [
                'entry_type' => PictureType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
