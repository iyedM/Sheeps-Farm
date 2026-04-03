<?php

namespace App\Aid\Form;

use App\Aid\Entity\AidCampagne;
use App\Aid\Entity\AidMarche;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AidMarcheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var AidCampagne|null $campagne */
        $campagne = $options['campagne'];

        $builder
            ->add('nom', TextType::class)
            ->add('date', DateType::class, ['widget' => 'single_text', 'input' => 'datetime_immutable'])
            ->add('responsable', EntityType::class, [
                'class' => User::class,
                'choice_label' => fn (User $user) => $user->getFullName() ?: $user->getEmail(),
                'placeholder' => 'Choisir un responsable',
                'required' => false,
                'attr' => ['class' => 'select2-enabled'],
            ])
            ->add('reduction', MoneyType::class, ['currency' => 'TND', 'required' => false])
            ->add('lignes', CollectionType::class, [
                'entry_type' => AidMarcheLigneType::class,
                'entry_options' => ['campagne' => $campagne],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AidMarche::class,
            'campagne' => null,
        ]);

        $resolver->setAllowedTypes('campagne', ['null', AidCampagne::class]);
    }
}
