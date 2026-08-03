<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\PointCountingStrategyRepository;
use App\Service\Ranking\PositionService;
use App\Service\Ranking\UsersGamesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/ranking')]
class RankingController extends AbstractController
{
    #[Route('/')]
    public function index(
        PositionService $positionService,
        UsersGamesService $usersGamesService,
        PointCountingStrategyRepository $pointCountingStrategyRepository,
        Request $request,
    ): Response {
        $pointCountingStrategyId = $request->query->get('pointCountingStrategies');

        $strategy = $pointCountingStrategyRepository->getByIdOrDefault($pointCountingStrategyId ? (int) $pointCountingStrategyId : null);
        $ranking = $usersGamesService->getUsersGames($strategy->getId());

        usort($ranking, fn ($a, $b) => $b['pointsYesterday'] <=> $a['pointsYesterday']);
        foreach ($ranking as $key => $user) {
            $ranking[$key]['positionYesterday'] = $positionService->getPosition($user['pointsYesterday']);
        }

        $positionService->resetPositions();

        usort($ranking, fn ($a, $b) => $b['points'] <=> $a['points']);
        foreach ($ranking as $key => $user) {
            $ranking[$key]['position'] = $positionService->getPosition($user['points']);
            $ranking[$key]['positionChange'] = $ranking[$key]['position'] - $ranking[$key]['positionYesterday'];
            $ranking[$key]['todayPoints'] = $ranking[$key]['points'] - $ranking[$key]['pointsYesterday'];
            $ranking[$key]['maxPoints'] = $strategy->getMaxPointsPerGame();
        }

        return $this->json(
            $ranking,
            context: [
                'groups' => ['ranking:list'],
            ]
        );
    }
}
