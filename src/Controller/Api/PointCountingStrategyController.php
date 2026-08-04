<?php

namespace App\Controller\Api;

use App\Repository\PointCountingStrategyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class PointCountingStrategyController extends AbstractController
{
    #[Route('/api/point-counting-strategies', name: 'app_point_counting_strategies', methods: ['GET'])]
    public function list(
        PointCountingStrategyRepository $repository,
    ): JsonResponse {
        $lang = 'pl';

        return $this->json(
            data: $repository->findAllWithTranslation($lang),
            context: ['groups' => ['point_counting_strategy:read']]
        );
    }
}
