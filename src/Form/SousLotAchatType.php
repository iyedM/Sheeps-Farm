<?php

namespace App\Form;

use App\Entity\Grange;
use App\Entity\Mouton;
use App\Entity\SousLotAchat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SousLotAchatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('race', TextType::class)
            ->add('age', IntegerType::class)
            ->add('genre', ChoiceType::class, [
                'choices' => ['Male' => Mouton::GENRE_MALE, 'Femelle' => Mouton::GENRE_FEMELLE],
            ])
            ->add('prix', MoneyType::class, ['currency' => 'TND'])
            ->add('quantite', IntegerType::class)
            ->add('grange', EntityType::class, [
                'class' => Grange::class,
                'choice_label' => 'nom',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => SousLotAchat::class]);
    }
}
