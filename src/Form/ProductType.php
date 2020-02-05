<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255]),
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new Length(['max' => 255]),
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'expanded' => true,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('pricingPlanName', TextType::class, [
                'label' => 'Pricing plan name',
                'mapped' => false,
                'constraints' => [
                    new Length(['max' => 255])
                ]
            ])
            ->add('pricingPlanAmount', NumberType::class, [
                'label' => 'Pricing plan amount (in USD cents)',
                'mapped' => false,
                'constraints' => [
                    new Length(['max' => 10])
                ]
            ])
            ->add('pricingPlanInterval', ChoiceType::class, [
                'choices' => array_combine(Product::PRICING_PLAN_INTERVAL, Product::PRICING_PLAN_INTERVAL),
                'expanded' => true,
                'mapped' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $product = $event->getForm()->getData();
            $product->setCreatedAt(new \DateTime());
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
