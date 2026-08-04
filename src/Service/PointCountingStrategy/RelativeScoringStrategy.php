<?php

namespace App\Service\PointCountingStrategy;

use App\Entity\User\UserGame;
use App\Enum\RelativeScoringStrategyTypeEnum;
use App\Enum\RelativeScoringStrategyTypeEnum as PointsTypeEnum;

class RelativeScoringStrategy extends AbstractStrategy
{
    private const string CODE = 'relative_scoring';

    public const int MAX_POINTS = 15;

    /**
     * @param array<UserGame> $usersGames
     */
    protected function calculatePoints(array $usersGames): void
    {
        $usersGames = $this->filterUserGamesToCalculate($usersGames);
        if (empty($usersGames)) {
            return;
        }

        $this->setPoints($usersGames);
    }

    /**
     * @param array<UserGame> $usersGames
     *
     * @return array<UserGame>
     */
    private function filterUserGamesToCalculate(array $usersGames): array
    {
        $array = [];
        foreach ($usersGames as $userGame) {
            if ($this->isScore($userGame)) {
                $array[] = $userGame;
            }
        }

        return $array;
    }

    protected function isScore(UserGame $userGame): bool
    {
        $game = $userGame->getGame();

        if (
            $userGame->getHomeGoals() === $userGame->getAwayGoals()
        ) {
            if ($game->getHomeGoals() === $game->getAwayGoals()) {
                return true;
            }

            return false;
        }

        if (
            $game->getHomeGoals() !== $game->getAwayGoals()
            && $userGame->getHomeGoals() > $userGame->getAwayGoals() == $game->getHomeGoals() > $game->getAwayGoals()
        ) {
            return true;
        }

        return false;
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

    /**
     * @param UserGame[] $usersGames
     */
    private function setPoints(array $usersGames): void
    {
        $homeGoals = $usersGames[0]->getGame()->getHomeGoals();
        $awayGoals = $usersGames[0]->getGame()->getAwayGoals();

        foreach ($usersGames as $key => $userGame) {
            if (
                $userGame->getHomeGoals() === $homeGoals
                && $userGame->getAwayGoals() === $awayGoals
            ) {
                $this->createUpdatePoints(PointsTypeEnum::POINT_TYPE_SCORE->value, self::MAX_POINTS, $userGame);
                unset($usersGames[$key]);
            } else {
                $this->createUpdatePoints(PointsTypeEnum::POINT_TYPE_WINER->value, 3, $userGame);

                if ($homeGoals != $awayGoals) {
                    if (
                        ($userGame->getHomeGoals() - 1 == $homeGoals && $userGame->getAwayGoals() == $awayGoals)
                        || ($userGame->getHomeGoals() + 1 == $homeGoals && $userGame->getAwayGoals() == $awayGoals)
                        || ($userGame->getAwayGoals() - 1 == $awayGoals && $userGame->getHomeGoals() == $homeGoals)
                        || ($userGame->getAwayGoals() + 1 == $awayGoals && $userGame->getHomeGoals() == $homeGoals)
                    ) {
                        $this->createUpdatePoints(PointsTypeEnum::POINT_TYPE_ONE_GOAL_DIFFERENCE->value, 1, $userGame);
                    }
                } elseif (
                    ($userGame->getHomeGoals() - 1 == $homeGoals && $userGame->getAwayGoals() - 1 == $awayGoals)
                    || ($userGame->getHomeGoals() + 1 == $homeGoals && $userGame->getAwayGoals() + 1 == $awayGoals)
                ) {
                    $this->createUpdatePoints(PointsTypeEnum::POINT_TYPE_ONE_GOAL_DIFFERENCE->value, 1, $userGame);
                }
            }
        }

        $targetSum = $homeGoals + $awayGoals;
        $targetDiff = $homeGoals - $awayGoals;

        // 2. Closest goal totals
        $this->processClosest(
            $usersGames,
            fn (UserGame $g) => abs(($g->getHomeGoals() + $g->getAwayGoals()) - $targetSum),
            PointsTypeEnum::POINT_TYPE_GOALS_COUNT
        );

        // 3. Closest goal differences
        $this->processClosest(
            $usersGames,
            fn (UserGame $g) => abs(($g->getHomeGoals() - $g->getAwayGoals()) - $targetDiff),
            PointsTypeEnum::POINT_TYPE_GOALS_DIFFERENCE
        );
    }

    /**
     * @param UserGame[]              $usersGames
     * @param callable(UserGame): int $distanceCalculator
     */
    private function processClosest(array $usersGames, callable $distanceCalculator, PointsTypeEnum $type): void
    {
        $groups = [];

        foreach ($usersGames as $userGame) {
            if (
                null === $userGame->getHomeGoals()
                || null === $userGame->getAwayGoals()
            ) {
                continue;
            }

            $distance = $distanceCalculator($userGame);

            $groups[$distance] ??= [];
            $groups[$distance][] = $userGame;
        }

        ksort($groups);

        $rank = 0;

        foreach ($groups as $games) {
            ++$rank;

            if ($rank > 3) {
                break;
            }

            foreach ($games as $userGame) {
                $this->createUpdatePoints($type->value, 4 - $rank, $userGame);
            }
        }
    }

    public function getPointTypes(): array
    {
        return RelativeScoringStrategyTypeEnum::getValues();
    }
}
