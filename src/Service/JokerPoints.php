<?php

namespace App\Service;

use App\Repository\User\JokerRepository;

readonly class JokerPoints
{
    public function __construct(
        private JokerRepository $jokerRepository,
    ) {
    }

    /**
     * @return array<int, int>
     */
    public function getJokersPoints(): array
    {
        $jokersArray = $this->jokerRepository->findAll();
        $jokersOrder = [];
        foreach ($jokersArray as $joker) {
            $jokersOrder[$joker->getUser()->getId()] = $joker->getPoints();
        }

        return $jokersOrder;
    }
}
