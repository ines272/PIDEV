<?php

namespace App\Form;

use App\Entity\Announcement;
use App\Enum\TypeGuard;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnouncementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address')
            ->add('longitude', NumberType::class)
            ->add('altitude', NumberType::class)
            ->add('careType', ChoiceType::class, [
                'choices' => TypeGuard::cases(),
                'choice_label' => fn($choice) => $choice->name,
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('visitPerDay')
            ->add('renumerationMin', NumberType::class)
            ->add('renumerationMax', NumberType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Announcement::class,
        ]);
    }

    

}
