<?php

namespace App\Form;

use App\Entity\CommerceVente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommerceVenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client', TextType::class)
            ->add('numeroClient', TextType::class)
            ->add('race', TextType::class)
            ->add('quantite', IntegerType::class)
            ->add('prixUnitaire', MoneyType::class, ['currency' => 'TND'])
            ->add('prixAdditionnel', MoneyType::class, ['currency' => 'TND'])
            ->add('dateVente', DateType::class, ['widget' => 'single_text', 'input' => 'datetime_immutable']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => CommerceVente::class]);
    }
}
