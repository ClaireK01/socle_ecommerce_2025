<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class PictureType extends AbstractType
{
    public function __construct(private readonly RouterInterface $router)
    {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('path', TextType::class, [
                'attr' =>[
                    "hidden" => true,
                    "data-path" => "1"
                ],
                'label_attr' =>[
                    "style" => 'display : none',
                ],
            ])
            ->add('addFile', ButtonType::class, [
                'label' => "<i class='fa-solid fa-pen'></i>",
                'attr' => [
                    'data-form-collection-target' => "fileManager",
                    'data-url' => $this->router->generate("file_manager", ['conf' => 'default', 'json' => true]),
                ],
                'label_html' => true,
            ])
            ->add('deleteFile', ButtonType::class, [
                'label' => "<i class='fa-solid fa-trash'></i>",
                'attr' => [
                    'data-form-collection-target' => "deleteForm",
                ],
                'label_html' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}
