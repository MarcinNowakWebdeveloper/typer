<?php

namespace App\Repository\User;

use App\Entity\Game;
use App\Entity\User\UserGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserGame>
 */
class UserGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserGame::class);
    }

    public function findByGameAndUserId(int $gameId, int $userId): ?UserGame
    {
        return $this->createQueryBuilder('ug')
            ->join('ug.user', 'u')
            ->join('ug.game', 'g')
            ->where('g.id = :gameId')
            ->andWhere('u.id = :userId')
            ->setParameter('gameId', $gameId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param array<string, string>|null $orderBy
     *
     * @return array<UserGame>
     */
    public function findByGame(Game $game, ?int $strategyId, ?array $orderBy = []): array
    {
        $query = $this->createQueryBuilder('ug')
            ->join('ug.game', 'g')
            ->join('ug.user', 'u')
            ->leftJoin('ug.userGamePoints', 'ugp')
            ->leftJoin('ugp.pointCountingStrategy', 'pcs')
            ->addSelect('u', 'ugp', 'pcs')
            ->where('g.id = :gameId')
            ->setParameter('gameId', $game->getId())
        ;

        foreach ($orderBy as $field => $order) {
            $query->addOrderBy($field, $order);
        }

        if ($strategyId) {
            $query->andWhere('(pcs.id = :strategyId OR pcs.id IS NULL)')
                ->setParameter('strategyId', $strategyId);
        } else {
            $query->andWhere('(pcs.isDefault = true OR pcs.id IS NULL)');
        }

        return $query->getQuery()->getResult();
    }

    public function getPointsByUserId(int $userId, ?int $strategyId = null): ?int
    {
        $query = $this->createQueryBuilder('ug')
            ->select('SUM(ugp.points) AS points')
            ->join('ug.user', 'u')
            ->join('ug.userGamePoints', 'ugp')
            ->join('ugp.pointCountingStrategy', 'pcs')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId);

        if ($strategyId) {
            $query->andWhere('pcs.id = :strategyId')
                ->setParameter('strategyId', $strategyId);
        } else {
            $query->andWhere('pcs.isDefault = true');
        }

        return $query
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param array<string, string> $orderBy
     *
     * @return array<UserGame>
     */
    public function findWithPoints(int $strategyId, array $orderBy = []): array
    {
        $queryBuilder = $this->createQueryBuilder('ug');
        $expr = $queryBuilder->expr();
        $queryBuilder
            ->addSelect('u, j, t, l, g, sg, s, ugp')
            ->join('ug.user', 'u')
            ->join('ug.game', 'g')
            ->join('g.stageGroup', 'sg')
            ->join('sg.stage', 's')
            ->leftJoin('u.joker', 'j')
            ->leftJoin('j.team', 't')
            ->leftJoin('t.logo', 'l')
            ->leftJoin(
                'ug.userGamePoints',
                'ugp',
                'WITH',
                'ugp.pointCountingStrategy = :strategyId'
            )
            ->where($expr->andX(
                $expr->isNotNull('g.homeGoals'),
                $expr->isNotNull('g.awayGoals'),
            ))
            ->orderBy('g.date', 'DESC')
            ->setParameter('strategyId', $strategyId);

        if ($orderBy) {
            foreach ($orderBy as $field => $order) {
                $queryBuilder->addOrderBy($field, $order);
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
