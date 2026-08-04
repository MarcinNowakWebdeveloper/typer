<?php

namespace App\Service\PointCountingStrategy;

use App\Entity\User\UserGame;

interface StrategyInterface
{
    /**
     * @param array<UserGame> $usersGames
     */
    public function calculate(array $usersGames): void;

    public static function getCode(): string;

    /**
     * @return array<string>
     */
    public function getPointTypes(): array;
}
