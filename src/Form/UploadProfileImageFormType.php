<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class UploadProfileImageFormType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('picture', FileType::class, [
            'label_attr' => [
                'class' => 'd-none'
            ],
            'row_attr' => [
                'class' => 'w-75 mb-4'
            ],
            'constraints' => [
                new Image([
                    'maxSize' => '10M'
                ])
            ]
        ])
        ->add('Upload_profile_picture', SubmitType::class)
        ;
    }

}