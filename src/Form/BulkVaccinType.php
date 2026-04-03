<?php

namespace App\Form;

use App\Entity\Mouton;
use App\Entity\Vaccin;
use App\Repository\MoutonRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BulkVaccinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['mapped' => false])
            ->add('dateVaccination', DateType::class, ['widget' => 'single_text', 'input' => 'datetime_immutable', 'mapped' => false])
            ->add('moutons', EntityType::class, [
                'class' => Mouton::class,
                'choice_label' => fn (Mouton $mouton) => sprintf('#%d - %s (%s)', $mouton->getId(), $mouton->getRace(), $mouton->getGenre()),
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
                'query_builder' => fn (MoutonRepository $repo) => $repo->createQueryBuilder('m')->orderBy('m.race', 'ASC'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => null]);
    }
}
