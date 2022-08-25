<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangePasswordFormType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => 'Passwords do not match!',
                'first_options' => ['label' => 'New password'],
                'second_options' =>['label' => 'Repeat password']
            ])
            ->add('Change', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-2 mb-5'
                ],
                'row_attr' => [
                    'class' => 'w-75'
                ],
            ])
        ;
    }

}