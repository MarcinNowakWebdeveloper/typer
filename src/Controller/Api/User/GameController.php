<?php

namespace App\Controller\Api\User;

use App\Controller\AbstractBaseController;
use App\Entity\Game;
use App\Entity\User;
use App\Entity\User\UserGame;
use App\Exception\InvalidDataException;
use App\Repository\GameRepository;
use App\Service\User\UserGameManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/user/game')]
class GameController extends AbstractBaseController
{
    public function __construct(
        private UserGameManager $userGameManager,
        protected ValidatorInterface $validator,
        protected TranslatorInterface $translator,
    ) {
    }

    #[Route('s', name: 'app_api_user_games', methods: ['POST'])]
    public function index(
        Request $request,
        GameRepository $gameRepository,
    ): JsonResponse {
        /** @var array{gamesIds: array<int>} $data */
        $data = $this->getAndValidData($request, ['gamesIds'], 'stage');
        /** @var User $user */
        $user = $this->getUser();
        $userGames = $gameRepository->getUserGamesByGamesIdsAndUserId($data['gamesIds'], $user->getId());

        return $this->json(
            array_map(
                fn (UserGame $userGame) => [
                    'id' => $userGame->getGame()->getId(),
                    'homeGoals' => $userGame->getHomeGoals(),
                    'awayGoals' => $userGame->getAwayGoals(),
                ],
                $userGames
            )
        );
    }

    #[Route('/edit:{game}', name: 'app_api_user_game_edit', methods: ['POST'])]
    public function edit(
        Game $game,
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        $now = new \DateTime();
        if ($game->getDate() < $now) {
            $message = $this->translator->trans('access.userGame.notEditable', [], 'errors');

            return $this->json(
                data: ['message' => $message],
                status: Response::HTTP_FORBIDDEN,
            );
        }

        try {
            /** @var array{homeGoals: int, awayGoals: int} $data */
            $data = $this->getAndValidData($request, ['homeGoals', 'awayGoals'], 'userGame');
            /** @var User $user */
            $user = $this->getUser();
            $userGame = $this->userGameManager->updateFromArray($user, $game, $data);
            $this->validate($userGame);
        } catch (InvalidDataException $e) {
            return $this->json(
                data: ['message' => $e->getMessage()],
                status: $e->getCode(),
            );
        }

        $em->flush();

        return $this->json([
            'success' => true,
        ]);
    }
}
