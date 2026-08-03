<?php

namespace App\Service\Ranking;

class PositionService
{
    private int $actualPosition = 1;
    private int $position = 1;

    private ?float $actualPoints = null;

    public function getPosition(float $points): int
    {
        if (
            null === $this->actualPoints
            || $points === $this->actualPoints
        ) {
            ++$this->position;
            $this->actualPoints = $points;

            return $this->actualPosition;
        }
        $this->actualPosition = $this->position;
        $this->actualPoints = $points;
        ++$this->position;

        return $this->actualPosition;
    }

    public function resetPositions(): void
    {
        $this->position = 1;
        $this->actualPosition = 1;
        $this->actualPoints = null;
    }
}
