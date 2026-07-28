<?php

namespace App\Controller\Api\Admin\Config;

use App\Controller\AbstractBaseController;
use App\Entity\Stage\Group;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/config/stage/group')]
class StageGroupController extends AbstractBaseController
{
    #[Route(':{group}', name: 'app_config_stage_group', methods: ['GET'])]
    public function group(Group $group): JsonResponse
    {
        return $this->json(
            data: $group,
            context: ['groups' => ['admin:stage:group:view']],
        );
    }
}
