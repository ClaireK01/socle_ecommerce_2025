<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    CONST TYPE = ["inscription" => 1, 'profile' => 2];
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        if($options['type'] == self::TYPE['profile']){
            $builder
                ->add("email")
                ->add("username")
                 ->add('password', RepeatedType::class, [
                     "label" => "profile.password.new",
                     'type' => PasswordType::class,
                     'invalid_message' => 'The password fields must match.',
                     'options' => ['attr' => ['class' => 'password-field']],
                     'required' => false,
                     'mapped' => false,
                     'first_options'  => ['label' => "profile.password.new"],
                     'second_options' => ['label' => "profile.password.repeatnew"],
                 ]);

            ;
        }



    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'type' => null,
            'field' => null,
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }
}
