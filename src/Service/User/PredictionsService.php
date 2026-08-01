<?php

namespace App\Service\User;

use App\Entity\Game;
use App\Repository\PointCountingStrategyRepository;
use App\Repository\User\UserGameRepository;
use App\Repository\UserRepository;

class PredictionsService
{
    public function __construct(
        private UserGameRepository $userGameRepository,
        private UserRepository $userRepository,
        private PointCountingStrategyRepository $pointCountingStrategyRepository,
    ) {
    }

    /**
     * @return array{expired: false, data: array<string, array{name: string, color: string}>}|array{}
     */
    public function getResponseForNotExpired(Game $game): array
    {
        $userGames = $this->userGameRepository->findByGame($game, null, ['u.name' => 'ASC']);

        $userGamesArray = [];
        $users = $this->userRepository->findActiveIndexedById();
        foreach ($users as $user) {
            $userGamesArray['unset'][$user->getId()] = [
                'name' => $user->getName(),
                'color' => $user->getColor(),
            ];
        }
        foreach ($userGames as $userGame) {
            $user = $userGame->getUser();
            unset($userGamesArray['unset'][$user->getId()]);
            $userGamesArray['set'][] = [
                'name' => $user->getName(),
                'color' => $user->getColor(),
            ];
        }

        return [
            'expired' => false,
            'data' => $userGamesArray,
        ];
    }

    /**
     * @return array{
     *     expired: true,
     *     data: list<array{
     *          prediction: string,
     *          users: array<''|int,
     *              array{
     *                  name: string|null,
     *                  color: string|null
     *              }
     *          >
     *     }>
     * }
     */
    public function getResponseForExpired(Game $game, ?int $strategyId = null): array
    {
        $userGames = $this->userGameRepository->findByGame(
            $game,
            $strategyId,
            ['ug.homeGoals' => 'DESC', 'ug.awayGoals' => 'DESC']
        );

        $userGamesArray = [];
        foreach ($userGames as $userGame) {
            $code = $userGame->getHomeGoals().'-'.$userGame->getAwayGoals();
            if (!isset($userGamesArray[$code])) {
                $userGamesArray[$code] = [
                    'prediction' => $code,
                    'points' => $userGame->getPoints(),
                ];
            }
            $user = $userGame->getUser();

            $userGamesArray[$code]['users'][$user->getId()] = [
                'name' => $user->getName(),
                'color' => $user->getColor(),
            ];
        }

        uasort($userGamesArray, static function (array $a, array $b): int {
            return count($b['users']) <=> count($a['users']);
        });

        return [
            'expired' => true,
            'points_set' => !is_null($game->getHomeGoals()) && !is_null($game->getAwayGoals()),
            'data' => array_values($userGamesArray),
            'max_points' => $this->pointCountingStrategyRepository->getMaxPointByStrategyId($strategyId),
        ];
    }
}
