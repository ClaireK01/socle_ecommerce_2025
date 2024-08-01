<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    CONST TYPE = ['profile' => 1, "inscription" => 2, 'admin' => 3];
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if($options['type'] == self::TYPE['profile']){
            $builder
                ->add('email')
                ->add('username', TextType::class, [
                    'label_attr'=>[
                        'style'=>'display: none;'
                    ],
                    'attr'=>[
                        'style'=>'margin:2rem auto',
                    ],
                ])
                ->add('description', TextareaType::class, [

                ]);
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'type' => null,
        ]);
    }
}
