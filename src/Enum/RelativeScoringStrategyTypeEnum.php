<?php

namespace App\Enum;

enum RelativeScoringStrategyTypeEnum: string
{
    case POINT_TYPE_SCORE = 'relative_scoring_strategy.score';
    case POINT_TYPE_WINER = 'relative_scoring_strategy.winer';
    case POINT_TYPE_GOALS_COUNT = 'relative_scoring_strategy.goals_count';
    case POINT_TYPE_GOALS_DIFFERENCE = 'relative_scoring_strategy.goals_difference';
    case POINT_TYPE_ONE_GOAL_DIFFERENCE = 'relative_scoring_strategy.one_goal_difference';

    /**
     * @return array<string>
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
