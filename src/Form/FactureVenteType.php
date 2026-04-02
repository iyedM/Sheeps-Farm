<?php

namespace App\Form;

use App\Entity\FactureVente;
use App\Entity\Mouton;
use App\Repository\MoutonRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureVenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client', TextType::class)
            ->add('numeroClient', TextType::class)
            ->add('dateVente', DateType::class, ['widget' => 'single_text', 'input' => 'datetime_immutable'])
            ->add('prixAdditionnel', MoneyType::class, ['currency' => 'TND'])
            ->add('moutons', EntityType::class, [
                'class' => Mouton::class,
                'choice_label' => fn (Mouton $m) => sprintf('#%d - %s (%s)', $m->getId(), $m->getRace(), $m->getGenre()),
                'multiple' => true,
                'query_builder' => fn (MoutonRepository $repo) => $repo->findNotVendusQueryBuilder(),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => FactureVente::class]);
    }
}
