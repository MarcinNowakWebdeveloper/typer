<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Attribute\Route;

class RegulationsController extends AbstractController
{
    #[Route('/api/regulations', name: 'api_regulations', methods: ['GET'])]
    public function index(
        #[Autowire(env: 'resolve:REGULATIONS_PATH')]
        string $regulationsPath,
    ): BinaryFileResponse {
        return new BinaryFileResponse($regulationsPath);
    }
}
