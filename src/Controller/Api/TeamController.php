<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/team')]
class TeamController extends AbstractController
{
    #[Route('/list', name: 'app_config_team_list', methods: ['GET'])]
    public function index(
        TeamRepository $repository,
    ): JsonResponse {
        return $this->json(
            data: $repository->findBy([], ['name' => 'ASC']),
            context: ['groups' => ['team:list']],
        );
    }
}
