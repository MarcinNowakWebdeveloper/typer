<?php

namespace App\Service;

use App\Entity\Game;
use App\Service\PointCountingStrategy\StrategyInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class PointsCalculator
{
    /**
     * @param iterable<StrategyInterface> $strategies
     */
    public function __construct(
        #[AutowireIterator('app.scoring_strategy')]
        private iterable $strategies,
        private LoggerInterface $logger,
    ) {
    }

    public function calculate(Game $game): void
    {
        $usersGames = $game->getUsersGames()->toArray();

        foreach ($this->strategies as $strategy) {
            try {
                $strategy->calculate($usersGames);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), $e->getTrace());
                throw $e;
            }
        }
    }
}
