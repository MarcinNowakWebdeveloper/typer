<?php

namespace App\Service\PointCountingStrategy;

use App\Entity\PointCountingStrategy;
use App\Entity\User\UserGame;
use App\Entity\User\UserGamePoints;
use App\Repository\PointCountingStrategyRepository;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractStrategy implements StrategyInterface
{
    /** @var array<int, array<string, UserGamePoints>> */
    private array $pointsObjects = [];

    protected PointCountingStrategy $strategy;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PointCountingStrategyRepository $pointCountingStrategyRepository,
    ) {
    }

    /**
     * @param array<int, UserGame> $usersGames
     */
    public function calculate(array $usersGames): void
    {
        $this->strategy = $this->pointCountingStrategyRepository->findOneBy(['code' => $this->getCode()]);

        $this->setPointsObjects($usersGames);
        $this->calculatePoints($usersGames);
        $this->cleanOldPoints($usersGames);

        $this->entityManager->flush();
    }

    /**
     * @param array<int, UserGame> $usersGames
     */
    protected function cleanOldPoints(array $usersGames): void
    {
        foreach ($usersGames as $userGame) {
            foreach ($this->pointsObjects[$userGame->getId()] as $userGamePoint) {
                $userGame->removeUserGamePoint($userGamePoint);
            }
        }
    }

    /**
     * @param array<int, UserGame> $usersGames
     */
    public function setPointsObjects(array $usersGames): void
    {
        foreach ($usersGames as $userGame) {
            $this->pointsObjects[$userGame->getId()] = $this->indexPointsByType($userGame);
        }
    }

    protected function getUserGamePoint(string $type, int $userGameId): UserGamePoints
    {
        if (isset($this->pointsObjects[$userGameId][$type])) {
            return $this->pointsObjects[$userGameId][$type];
        }
        $userGamePoint = new UserGamePoints();
        $userGamePoint->setType($type);
        $this->entityManager->persist($userGamePoint);

        return $userGamePoint;
    }

    /** @return array<string, UserGamePoints> */
    private function indexPointsByType(UserGame $userGame): array
    {
        $points = [];
        foreach ($userGame->getUserGamePoints() as $userGamePoint) {
            if ($userGamePoint->getPointCountingStrategy()->getId() !== $this->strategy->getId()) {
                continue;
            }

            if (isset($points[$userGamePoint->getType()])) {
                $userGame->removeUserGamePoint($userGamePoint);

                continue;
            }
            $points[$userGamePoint->getType()] = $userGamePoint;
        }

        return $points;
    }

    protected function unsetPointsObjects(int $userGameId, string $type): void
    {
        if (!isset($this->pointsObjects[$userGameId][$type])) {
            return;
        }

        unset($this->pointsObjects[$userGameId][$type]);
    }

    /**
     * @param array<int, UserGame> $usersGames
     */
    abstract protected function calculatePoints(array $usersGames): void;
}
