<?php

namespace App\Form;

use App\Entity\Pet;
use App\Enum\Gender;
use App\Enum\PetType as PetTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('typePet', ChoiceType::class, [
                'choices' => PetTypeEnum::cases(),
                'choice_label' => fn($choice) => $choice->name,
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('breed')
            ->add('weight', NumberType::class)
            ->add('description', TextareaType::class)
            ->add('gender', ChoiceType::class, [
                'choices' => Gender::cases(),
                'choice_label' => fn($choice) => $choice->name,
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('isVacinated', CheckboxType::class, [
                'required' => false
            ])
            ->add('hasContagiousDisease', CheckboxType::class, [
                'required' => false
            ])
            ->add('hasMedicalRecord', CheckboxType::class, [
                'required' => false
            ])
            ->add('hasCriticalCondition', CheckboxType::class, [
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pet::class,
        ]);
    }
}
