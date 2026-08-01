<?php

namespace App\Command;

use App\Repository\GameRepository;
use App\Service\PointsCalculator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'app:calculate-points')]
class CalculatePoints
{
    public function __construct(
        private PointsCalculator $pointsCalculator,
        private GameRepository $gameRepository,
    ) {
    }

    public function __invoke(
    ): int {
        $games = $this->gameRepository->findWithGoalsSet();
        foreach ($games as $game) {
            $this->pointsCalculator->calculate($game);
        }

        return Command::SUCCESS;
    }
}
