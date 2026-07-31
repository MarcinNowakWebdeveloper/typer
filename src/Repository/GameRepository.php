<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\User\UserGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function getFirstGame(): ?Game
    {
        return $this->createQueryBuilder('g')
            ->orderBy('g.date', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param array<int, int> $gamesIds
     *
     * @return array<UserGame>
     */
    public function getUserGamesByGamesIdsAndUserId(array $gamesIds, int $userId): array
    {
        return $this->createQueryBuilder('g', 'g.id')
            ->select('ug')
            ->join(UserGame::class, 'ug', 'WITH', 'ug.game = g')
            ->join('ug.user', 'u')
            ->where('g.id IN (:gamesIds)')
            ->andWhere('u.id = :userId')
            ->setParameter('gamesIds', $gamesIds)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult()
        ;
    }
}
