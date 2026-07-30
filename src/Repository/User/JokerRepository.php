<?php

namespace App\Repository\User;

use App\Entity\User;
use App\Entity\User\Joker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Joker>
 */
class JokerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Joker::class);
    }

    public function findOneByUser(User $user): ?Joker
    {
        return $this->createQueryBuilder('j')
            ->where('j.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return array<Joker>
     */
    public function getAllWithTeamsAndUsers(): array
    {
        return $this->createQueryBuilder('j')
            ->select('j, t, u')
            ->join('j.team', 't')
            ->join('j.user', 'u')
            ->getQuery()
            ->getResult();
    }
}
