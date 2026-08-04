<?php

namespace App\Repository\User;

use App\Entity\User\UserGamePoints;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserGamePoints>
 */
class UserGamePointsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserGamePoints::class);
    }

    /**
     * @return array<int, array{type: string, count: int, id: int, name: string, color: string}>
     */
    public function countByTypeAndUserId(?int $strategyId): array
    {
        $qb = $this->createQueryBuilder('ugp');
        $qb->select('ugp.type, COUNT(ugp.id) as count, u.id, u.name, u.color')
            ->join('ugp.userGame', 'ug')
            ->join('ug.user', 'u')
            ->join('ugp.pointCountingStrategy', 'pcs')
            ->groupBy('u.id, u.name, u.color, ugp.type');

        if ($strategyId) {
            $qb->andWhere('pcs.id = :strategyId')
                ->setParameter('strategyId', $strategyId);
        } else {
            $qb->andWhere('pcs.isDefault = true');
        }

        return $qb->getQuery()->getResult();
    }
}
