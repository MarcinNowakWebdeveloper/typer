<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\PointCountingStrategyRepository;
use App\Service\Charts\PointsChartLine;
use App\Service\Charts\PointsTypesBarChart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/charts')]
class ChartsController extends AbstractController
{
    #[Route('/points_line')]
    public function pointsLine(
        PointsChartLine $chartLine,
        Request $request,
    ): JsonResponse {
        $pointCountingStrategyId = $request->query->get('pointCountingStrategies');
        $pointCountingStrategyId = $pointCountingStrategyId ? (int) $pointCountingStrategyId : null;

        return $this->json($chartLine->getData($pointCountingStrategyId));
    }

    #[Route('/points_types_bar')]
    public function pointsTypesBar(
        PointsTypesBarChart $barChart,
        Request $request,
    ): JsonResponse {
        $pointCountingStrategyId = $request->query->get('pointCountingStrategies');
        $pointCountingStrategyId = $pointCountingStrategyId ? (int) $pointCountingStrategyId : null;

        return $this->json($barChart->getData($pointCountingStrategyId));
    }
}
