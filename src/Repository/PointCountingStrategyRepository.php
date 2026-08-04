<?php

namespace App\Repository;

use App\Entity\PointCountingStrategy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PointCountingStrategy>
 */
class PointCountingStrategyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PointCountingStrategy::class);
    }

    public function getMaxPointByStrategyId(?int $strategyId): float
    {
        $query = $this->createQueryBuilder('p')
            ->select('p.maxPointsPerGame');

        if ($strategyId) {
            $query->where('p.id = :strategyId');
            $query->setParameter('strategyId', $strategyId);
        } else {
            $query->where('p.isDefault = true');
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    public function getByIdOrDefault(?int $id = null): ?PointCountingStrategy
    {
        $query = $this->createQueryBuilder('p');

        if (!$id) {
            $query->where('p.isDefault = true');

            return $query->getQuery()->getOneOrNullResult();
        }

        return $query
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<PointCountingStrategy>
     */
    public function findAllWithTranslation(string $lang): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.translations', 't')
            ->where('t.locale = :locale')
            ->setParameter('locale', $lang)
            ->getQuery()
            ->getResult();
    }
}
