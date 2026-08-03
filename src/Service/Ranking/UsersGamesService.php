<?php

namespace App\Service\Ranking;

use App\Entity\User\UserGame;
use App\Repository\Stage\StageViewRepository;
use App\Repository\User\UserGameRepository;
use App\Service\JokerPoints;

readonly class UsersGamesService
{
    public function __construct(
        private UserGameRepository $userGameRepository,
        private StageViewRepository $stageViewRepository,
        private JokerPoints $jokerPointsService,
    ) {
    }

    /**
     * @return array<int, array{
     *     stages: array<int, array{
     *          short_name: string,
     *          games: array<int, UserGame>
     *     }>,
     *     user: array{id: int, name: string},
     *     joker: array{team_name: string, team_logo: int},
     *     points: float,
     *     pointsYesterday: float,
     * }>
     */
    public function getUsersGames(int $strategyId): array
    {
        $stagesOrder = $this->getStageOrder();
        $jokerPoints = $this->jokerPointsService->getJokersPoints();

        $usersGames = [];
        $yesterday = new \DateTime('-1 day');
        $usersGamesArray = $this->userGameRepository->findWithPoints($strategyId, ['g.date' => 'DESC']);

        $defaultStage = [];
        foreach ($stagesOrder as $stageOrder) {
            $defaultStage[$stageOrder['key']] = [
                'short_name' => $stageOrder['short_name'],
                'games' => [],
            ];
        }

        foreach ($usersGamesArray as $userGame) {
            if (!isset($usersGames[$userGame->getUser()->getId()])) {
                $usersGames[$userGame->getUser()->getId()] = [
                    'stages' => $defaultStage,
                    'user' => [
                        'id' => $userGame->getUser()->getId(),
                        'name' => $userGame->getUser()->getName(),
                        'color' => $userGame->getUser()->getColor(),
                    ],
                    'joker' => [
                        'team_name' => $userGame->getUser()->getJoker()?->getTeam()->getName(),
                        'team_logo' => $userGame->getUser()->getJoker()?->getTeam()->getLogo()->getId(),
                        'points' => $jokerPoints[$userGame->getUser()->getId()] ?? null,
                    ],
                    'points' => $jokerPoints[$userGame->getUser()->getId()] ?? 0,
                    'pointsYesterday' => 0,
                ];
            }

            $stage = $userGame->getGame()->getStageGroup()->getStage();
            $usersGames[$userGame->getUser()->getId()]['stages'][$stagesOrder[$stage->getId()]['key']]['games'][] = $userGame;
            $usersGames[$userGame->getUser()->getId()]['points'] += $userGame->getPoints();

            if ($yesterday->getTimestamp() < $userGame->getGame()->getDate()->getTimestamp()) {
                continue;
            }
            $usersGames[$userGame->getUser()->getId()]['pointsYesterday'] += $userGame->getPoints();
        }

        return $usersGames;
    }

    /**
     * @return array<int, array{key: int, short_name: string}>
     */
    private function getStageOrder(): array
    {
        $stagesView = $this->stageViewRepository->findBy([], ['startDate' => 'DESC']);

        $stagesOrder = [];
        foreach ($stagesView as $key => $stageView) {
            $stagesOrder[$stageView->getStage()->getId()] = [
                'key' => $key,
                'short_name' => $stageView->getShortName(),
            ];
        }

        return $stagesOrder;
    }
}
