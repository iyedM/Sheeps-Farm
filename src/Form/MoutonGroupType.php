<?php

namespace App\Form;

use App\Entity\Grange;
use App\Entity\Mouton;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoutonGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('race', TextType::class)
            ->add('genre', ChoiceType::class, [
                'choices' => ['Male' => Mouton::GENRE_MALE, 'Femelle' => Mouton::GENRE_FEMELLE],
            ])
            ->add('ageInitialMois', IntegerType::class)
            ->add('dateAjout', DateType::class, ['widget' => 'single_text', 'input' => 'datetime_immutable'])
            ->add('grange', EntityType::class, [
                'class' => Grange::class,
                'choice_label' => 'nom',
            ])
            ->add('origine', ChoiceType::class, [
                'choices' => ['Interne' => Mouton::ORIGINE_INTERNE, 'Externe' => Mouton::ORIGINE_EXTERNE],
            ])
            ->add('prix', MoneyType::class, ['currency' => 'TND', 'required' => false])
            ->add('quantite', IntegerType::class, [
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Mouton::class]);
    }
}
