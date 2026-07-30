<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\User\JokerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class JokerController extends AbstractController
{
    #[Route('/api/jokers', name: 'app_joker_list', methods: ['GET'])]
    public function list(
        JokerRepository $repository,
    ): JsonResponse {
        $jokersArray = $repository->getAllWithTeamsAndUsers();
        $jokers = [];
        foreach ($jokersArray as $joker) {
            if (!isset($jokers[$joker->getTeam()->getName()])) {
                $jokers[$joker->getTeam()->getName()] = [
                    'team' => $joker->getTeam()->getName(),
                    'logo' => $joker->getTeam()->getLogo()->getId(),
                    'users' => [],
                ];
            }
            $jokers[$joker->getTeam()->getName()]['users'][] = $joker->getUser()->getName();
        }

        uasort($jokers, static function (array $a, array $b): int {
            return count($b['users']) <=> count($a['users']);
        });

        return $this->json(array_values($jokers));
    }
}
