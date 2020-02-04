<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Name',
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255])
                ]
            ])
//            ->add('roles', CollectionType::class, [
//                'label' => 'Roles',
//                'entry_type' => ChoiceType::class,
//                'entry_options' => [
//                'choices' => array_combine(User::ROLE_TYPES, User::ROLE_TYPES),
//                'multiple' => false,
//                ],
//                'constraints' => [
//                    new NotBlank(),
//                ]
//            ])
            ->add('roles', ChoiceType::class, [
                'choices' => array_combine(User::ROLE_TYPES, User::ROLE_TYPES),
                'expanded'=> true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Password',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                        'max' => 100,
                    ])
                ]
            ])
            ->add('phoneNumber', NumberType::class, [
                'label' => 'Phone number',
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
