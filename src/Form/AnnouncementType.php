<?php

namespace App\Form;

use App\Entity\Announcement;
use App\Entity\Pet;
use App\Repository\PetRepository;
use App\Enum\TypeGuard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class AnnouncementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            // 1️⃣ Animal
            ->add('pet', EntityType::class, [
                'class' => Pet::class,
                'choice_label' => 'name',
                'query_builder' => fn(PetRepository $repo) => $repo->createQueryBuilder('p')
                    ->andWhere('p.owner = :ownerId')
                    ->setParameter('ownerId', $user?->getId())
                    ->orderBy('p.name', 'ASC'),
                'placeholder' => 'Choisir un animal',
                'required' => true,
            ])

            // 2️⃣ Type de garde
            ->add('careType', ChoiceType::class, [
                'label' => 'Type de garde',
                'choices' => [
                    'Chez moi' => TypeGuard::CHEZ_MOI,
                    'En chenil' => TypeGuard::CHENIL,
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ])

            // 3️⃣ Adresse
            ->add('address')
             
            
            // 3️⃣ service
            ->add('services', TextType::class, [
    'required' => true,
])

             ->add('visitPerDay', NumberType::class, [
                'required' => false,
                'label' => 'Visites / jour',
            ])

            // 6️⃣ Horaires de visite
            ->add('visitHours', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => ['attr' => ['class' => 'input-modern'], 'label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
              
                'required' => false,
            ])

            // 5️⃣ Durée de garde
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
            ])

            // 6️⃣ Rémunération
            ->add('renumerationMin', NumberType::class)
            ->add('renumerationMax', NumberType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Announcement::class,
            'user' => null,
        ]);
    }
}