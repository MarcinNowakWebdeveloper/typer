<?php

namespace App\Service\User;

use App\Entity\Game;
use App\Repository\User\UserGameRepository;
use App\Repository\UserRepository;

class PredictionsService
{
    public function __construct(
        private UserGameRepository $userGameRepository,
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @return array{expired: false, data: array<string, array{name: string, color: string}>}|array{}
     */
    public function getResponseForNotExpired(Game $game): array
    {
        $userGames = $this->userGameRepository->findByGame($game, ['u.name' => 'ASC']);

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
    public function getResponseForExpired(Game $game): array
    {
        $userGames = $this->userGameRepository->findByGame($game, ['ug.homeGoals' => 'DESC', 'ug.awayGoals' => 'DESC']);

        $userGamesArray = [];
        foreach ($userGames as $userGame) {
            $code = $userGame->getHomeGoals().'-'.$userGame->getAwayGoals();
            if (!isset($userGamesArray[$code])) {
                $userGamesArray[$code] = ['prediction' => $code];
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
            'data' => array_values($userGamesArray),
        ];
    }
}
