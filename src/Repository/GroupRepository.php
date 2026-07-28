<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Group>
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    /**
     * @param array<int, int> $array
     *
     * @return array<int, Group>
     */
    public function findAndIndexById(array $array): array
    {
        $qb = $this->createQueryBuilder('g', 'g.id');
        $qb->where($qb->expr()->in('g.id', $array));

        return $qb->getQuery()->getResult();
    }
}
