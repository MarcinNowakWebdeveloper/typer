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
    public function findByGame(Game $game, ?array $orderBy = []): array
    {
        $query = $this->createQueryBuilder('ug')
            ->join('ug.game', 'g')
            ->join('ug.user', 'u')
            ->addSelect('u')
            ->where('g.id = :gameId')
            ->setParameter('gameId', $game->getId())
        ;

        foreach ($orderBy as $field => $order) {
            $query->addOrderBy($field, $order);
        }

        return $query->getQuery()->getResult();
    }
}
