<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Game;
use App\Service\User\PredictionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    #[Route('/api/game:{game}/predictions', name: 'app_game_predictions', methods: ['GET'])]
    public function index(
        Game $game,
        Request $request,
        PredictionsService $predictionsService,
    ): JsonResponse {
        $now = new \DateTime();
        $expired = $game->getDate() < $now;
        if (!$expired) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                return $this->json([]);
            }

            return $this->json($predictionsService->getResponseForNotExpired($game));
        }

        $pointCountingStrategyId = $request->query->get('pointCountingStrategies');
        $pointCountingStrategyId = $pointCountingStrategyId ? (int) $pointCountingStrategyId : null;
        $response = $predictionsService->getResponseForExpired($game, $pointCountingStrategyId);

        return $this->json($response);
    }
}
