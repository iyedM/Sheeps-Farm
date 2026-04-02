<?php

namespace App\Repository;

use App\Entity\Mouton;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mouton>
 */
class MoutonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouton::class);
    }

    public function getFilterQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.grange', 'g')->addSelect('g')
            ->orderBy('m.id', 'DESC');

        if (!empty($filters['race'])) {
            $qb->andWhere('m.race LIKE :race')->setParameter('race', '%' . $filters['race'] . '%');
        }
        if (!empty($filters['genre'])) {
            $qb->andWhere('m.genre = :genre')->setParameter('genre', $filters['genre']);
        }
        if (!empty($filters['origine'])) {
            $qb->andWhere('m.origine = :origine')->setParameter('origine', $filters['origine']);
        }
        if (isset($filters['ageMin']) && $filters['ageMin'] !== '') {
            $qb->andWhere('m.ageInitialMois >= :ageMin')->setParameter('ageMin', (int) $filters['ageMin']);
        }
        if (isset($filters['ageMax']) && $filters['ageMax'] !== '') {
            $qb->andWhere('m.ageInitialMois <= :ageMax')->setParameter('ageMax', (int) $filters['ageMax']);
        }
        if (!empty($filters['dateFrom'])) {
            $qb->andWhere('m.dateAjout >= :dateFrom')->setParameter('dateFrom', new \DateTimeImmutable($filters['dateFrom']));
        }
        if (!empty($filters['dateTo'])) {
            $qb->andWhere('m.dateAjout <= :dateTo')->setParameter('dateTo', new \DateTimeImmutable($filters['dateTo'] . ' 23:59:59'));
        }

        return $qb;
    }

    public function findWithFilters(array $filters, int $page): QueryBuilder
    {
        return $this->getFilterQueryBuilder($filters);
    }

    public function countVendus(): int
    {
        return (int) $this->createQueryBuilder('m')->select('COUNT(m.id)')->andWhere('m.estVendu = true')->getQuery()->getSingleScalarResult();
    }

    public function countNonVendus(): int
    {
        return (int) $this->createQueryBuilder('m')->select('COUNT(m.id)')->andWhere('m.estVendu = false')->getQuery()->getSingleScalarResult();
    }

    public function getByFieldCounts(string $field): array
    {
        return $this->createQueryBuilder('m')
            ->select(sprintf('m.%s as label', $field), 'COUNT(m.id) as total')
            ->groupBy(sprintf('m.%s', $field))
            ->getQuery()
            ->getArrayResult();
    }

    public function getNonVendusParGrange(): array
    {
        return $this->createQueryBuilder('m')
            ->select('g.nom as label', 'COUNT(m.id) as total')
            ->join('m.grange', 'g')
            ->andWhere('m.estVendu = false')
            ->groupBy('g.id')
            ->getQuery()
            ->getArrayResult();
    }

    public function getEvolutionParMois(): array
    {
        $moutons = $this->createQueryBuilder('m')
            ->select('m.dateAjout')
            ->orderBy('m.dateAjout', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $map = [];
        foreach ($moutons as $row) {
            $mois = $row['dateAjout']->format('Y-m');
            $map[$mois] = ($map[$mois] ?? 0) + 1;
        }

        $result = [];
        foreach ($map as $mois => $total) {
            $result[] = ['mois' => $mois, 'total' => $total];
        }

        return $result;
    }

    public function sumValeurCheptel(): float
    {
        return (float) ($this->createQueryBuilder('m')
            ->select('COALESCE(SUM(m.prix), 0)')
            ->andWhere('m.prix > 0')
            ->getQuery()->getSingleScalarResult() ?? 0);
    }

    public function findNotVendusQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.estVendu = false')
            ->orderBy('m.id', 'DESC');
    }
}
