<?php

namespace App\Service\User;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\User\UserGame;
use App\Repository\User\UserGameRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserGameManager
{
    public function __construct(
        private UserGameRepository $userGameRepository,
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * @param array{homeGoals: int, awayGoals: int} $data
     */
    public function updateFromArray(User $user, Game $game, array $data): UserGame
    {
        $userGame = $this->userGameRepository->findByGameAndUserId($game->getId(), $user->getId());
        if (!$userGame) {
            $userGame = new UserGame();
            $userGame->setUser($user);
            $userGame->setGame($game);
            $this->em->persist($userGame);
        }

        $userGame->setHomeGoals($data['homeGoals']);
        $userGame->setAwayGoals($data['awayGoals']);

        return $userGame;
    }
}
