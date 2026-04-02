<?php

namespace App\Form;

use App\Entity\CommerceAchat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommerceAchatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fournisseur', TextType::class)
            ->add('numeroFournisseur', TextType::class)
            ->add('race', TextType::class)
            ->add('quantite', IntegerType::class)
            ->add('prixUnitaire', MoneyType::class, ['currency' => 'TND'])
            ->add('dateAchat', DateType::class, ['widget' => 'single_text', 'input' => 'datetime_immutable']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => CommerceAchat::class]);
    }
}
