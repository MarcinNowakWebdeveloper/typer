<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return array{items: User[], total: int}
     */
    public function paginate(
        int $page,
        int $limit,
        string $status,
    ): array {
        $qb = $this->createQueryBuilder('u');

        switch ($status) {
            case 'active':
                $qb->andWhere('u.isActive = true');
                break;

            case 'inactive':
                $qb->andWhere('u.isActive = false');
                break;

            case 'unconfirmed':
                $qb->andWhere('u.isVerified = false');
                break;
        }

        $total = (clone $qb)
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $items = $qb
            ->orderBy('u.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return [
            'items' => $items,
            'total' => (int) $total,
        ];
    }

    /**
     * @return array{total: int, active: int, inactive: int, unconfirmed: int}
     */
    public function getStats(): array
    {
        return [
            'total' => $this->count(),

            'active' => $this->count([
                'isActive' => true,
            ]),

            'inactive' => $this->count([
                'isActive' => false,
            ]),

            'unconfirmed' => $this->count([
                'isVerified' => false,
            ]),
        ];
    }
}
