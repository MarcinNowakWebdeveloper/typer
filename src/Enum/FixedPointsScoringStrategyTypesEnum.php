<?php

namespace App\Enum;

enum FixedPointsScoringStrategyTypesEnum: string
{
    case POINT_TYPE_SCORE = 'fixed_points_scoring_strategy.score';
    case POINT_TYPE_WINER = 'fixed_points_scoring_strategy.winer';
    case POINT_TYPE_GOALS_COUNT = 'fixed_points_scoring_strategy.goals_count';
    case POINT_TYPE_ONE_GOAL_DIFFERENCE = 'fixed_points_scoring_strategy.one_goal_difference';

    /**
     * @return array<string>
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
