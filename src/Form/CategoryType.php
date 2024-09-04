<?php

namespace App\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
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
            ->add('parent', EntityType::class, [
                'class' => Category::class,
                "placeholder" => "Categorie parente",
                "query_builder" => function (CategoryRepository $r) {
                    return $r->createQueryBuilder('c');
                },
                "autocomplete" => true,
                'multiple' => false,
                "attr" => [
                    'id' => "tom-select-category"
                ],
                'required' => false,
            ])
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
            'data_class' => Category::class,
        ]);
    }
}
