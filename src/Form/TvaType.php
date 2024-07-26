<?php

namespace App\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use App\Entity\Tva;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TvaType extends AbstractType
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
            ->add('rate', ChoiceType::class, [
                'choices' => [
                    '5.5%'=> 5.5,
                    '10%'=> 10,
                    '20%'=> 20,
                ],
                "attr" => [
                    'id' => "tom-select-tva"
                ],
                'autocomplete' => true
            ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tva::class,
        ]);
    }
}
