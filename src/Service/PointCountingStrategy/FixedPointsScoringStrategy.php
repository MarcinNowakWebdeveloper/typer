<?php

namespace App\Service\PointCountingStrategy;

use App\Entity\User\UserGame;
use App\Enum\FixedPointsScoringStrategyTypesEnum as PointsTypeEnum;

class FixedPointsScoringStrategy extends AbstractStrategy
{
    private const string CODE = 'fixed_points_scoring';

    public const int MAX_POINTS = 12;

    protected function calculatePoints(array $usersGames): void
    {
        foreach ($usersGames as $userGame) {
            $this->setPointsForUserGame($userGame);
        }
    }

    private function setPointsForUserGame(UserGame $userGame): void
    {
        $game = $userGame->getGame();

        /* exact result */
        if (
            $userGame->getHomeGoals() === $game->getHomeGoals()
            && $userGame->getAwayGoals() === $game->getAwayGoals()
        ) {
            $this->createUpdatePoints(PointsTypeEnum::POINT_TYPE_SCORE->value, self::MAX_POINTS, $userGame);

            return;
        }

        /* successful draw */
        if ($userGame->getHomeGoals() === $userGame->getAwayGoals()) {
            if ($game->getHomeGoals() === $game->getAwayGoals()) {
                $this->createUpdatePoints(PointsTypeEnum::POINT_TYPE_WINER->value, 5, $userGame);
            }
        } elseif ( /* hit winner */
            $game->getHomeGoals() != $game->getAwayGoals()
            && ($userGame->getHomeGoals() > $userGame->getAwayGoals() == $game->getHomeGoals() > $game->getAwayGoals())
        ) {
            $this->createUpdatePoints(PointsTypeEnum::POINT_TYPE_WINER->value, 5, $userGame);
        }

        /* hit number of goals */
        if (
            $game->getHomeGoals() + $game->getAwayGoals()
            === $userGame->getHomeGoals() + $userGame->getAwayGoals()
        ) {
            $this->createUpdatePoints(PointsTypeEnum::POINT_TYPE_GOALS_COUNT->value, 3, $userGame);
        }

        /* one goal difference */
        if (
            ($userGame->getHomeGoals() - 1 == $game->getHomeGoals() && $userGame->getAwayGoals() == $game->getAwayGoals())
            || ($userGame->getHomeGoals() + 1 == $game->getHomeGoals() && $userGame->getAwayGoals() == $game->getAwayGoals())
            || ($userGame->getAwayGoals() - 1 == $game->getAwayGoals() && $userGame->getHomeGoals() == $game->getHomeGoals())
            || ($userGame->getAwayGoals() + 1 == $game->getAwayGoals() && $userGame->getHomeGoals() == $game->getHomeGoals())
        ) {
            $this->createUpdatePoints(PointsTypeEnum::POINT_TYPE_ONE_GOAL_DIFFERENCE->value, 1, $userGame);
        }
    }

    private function createUpdatePoints(string $type, float $points, UserGame $userGame): void
    {
        $userGamePoint = $this->getUserGamePoint($type, $userGame->getId());
        $userGamePoint->setPoints($points);
        $userGamePoint->setPointCountingStrategy($this->strategy);
        $userGame->addUserGamePoint($userGamePoint);
        $this->unsetPointsObjects($userGame->getId(), $type);
    }

    public static function getCode(): string
    {
        return self::CODE;
    }
}
