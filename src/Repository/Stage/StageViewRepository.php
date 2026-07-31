<?php

namespace App\Repository\Stage;

use App\Entity\Stage\StageView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StageView>
 */
class StageViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StageView::class);
    }

    public function getLastStage(): ?StageView
    {
        $queryBuilder = $this->createQueryBuilder('sv');
        $queryBuilder
            ->select('s.id')
            ->join('sv.stage', 's')
            ->orderBy('sv.startDate', 'DESC')
            ->setMaxResults(1);

        $id = $queryBuilder->getQuery()->getSingleScalarResult();

        return $this->getFullStage($id);
    }

    public function getCurrentStage(): ?StageView
    {
        $queryBuilder = $this->getFullStageQueryBuilder();
        $expr = $queryBuilder->expr();

        $queryBuilder
            ->orderBy('g.date', 'ASC')
            ->where(
                $expr->orX(
                    $expr->gte('sv.startDate', ':date'),
                    $expr->andX(
                        $expr->gte('sv.endDate', ':date'),
                        $expr->lt('sv.startDate', ':date')
                    )
                )
            )
            ->setParameter('date', new \DateTime());

        return $queryBuilder
            ->getQuery()
            ->getResult()[0] ?? null;
    }

    public function getFullStage(int $stageId): ?StageView
    {
        $queryBuilder = $this->getFullStageQueryBuilder();
        $queryBuilder->where('s.id = :stageId')
            ->setParameter('stageId', $stageId);

        return $queryBuilder
            ->getQuery()
            ->getResult()[0] ?? null;
    }

    private function getFullStageQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('sv');
        $queryBuilder
            ->addSelect('s, sg, gr, g, ht, at')
            ->join('sv.stage', 's')
            ->join('s.groups', 'sg')
            ->join('sg.group', 'gr')
            ->join('sg.games', 'g')
            ->join('g.homeTeam', 'ht')
            ->join('g.awayTeam', 'at');

        return $queryBuilder;
    }
}
