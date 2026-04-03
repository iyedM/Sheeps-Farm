<?php

namespace App\Aid\Form;

use App\Aid\Entity\AidCampagne;
use App\Aid\Entity\AidLot;
use App\Aid\Entity\AidMarcheLigne;
use App\Aid\Repository\AidLotRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AidMarcheLigneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var AidCampagne|null $campagne */
        $campagne = $options['campagne'];

        $builder
            ->add('lot', EntityType::class, [
                'class' => AidLot::class,
                'placeholder' => 'Sélectionner un lot',
                'attr' => ['class' => 'select2-enabled'],
                'choice_label' => fn (AidLot $lot) => sprintf(
                    'Lot #%d — stock %d — restant %d — %s TND',
                    $lot->getId(),
                    $lot->getQuantite(),
                    $lot->getQuantiteRestante(),
                    $lot->getPrixUnitaire()
                ),
                'choice_attr' => fn (AidLot $lot) => [
                    'data-restant' => (string) $lot->getQuantiteRestante(),
                ],
                'query_builder' => function (AidLotRepository $repository) use ($campagne) {
                    $builder = $repository->createQueryBuilder('l')->orderBy('l.id', 'ASC');

                    if ($campagne) {
                        $builder->andWhere('l.campagne = :campagne')->setParameter('campagne', $campagne);
                    }

                    return $builder;
                },
            ])
            ->add('quantiteAmenes', IntegerType::class)
            ->add('quantiteVendus', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AidMarcheLigne::class,
            'campagne' => null,
        ]);

        $resolver->setAllowedTypes('campagne', ['null', AidCampagne::class]);
    }
}