<?php

namespace App\Controller\Api;

use App\Entity\Stage\StageView;
use App\Repository\Stage\StageViewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/stage')]
class StageController extends AbstractController
{
    public const string GAMES_GROUP_FUTURE = 'future';
    public const string GAMES_GROUP_PAST = 'past';

    #[Route('', name: 'api_stage_current', methods: ['GET'])]
    public function currentStage(StageViewRepository $repository): JsonResponse
    {
        $stage = $repository->getCurrentStage();
        if (!$stage) {
            $stage = $repository->getLastStage();
        }
        $stages = $repository->findBy([], ['startDate' => 'ASC']);

        return $this->json(
            data: [
                'stage' => $this->normalizeStage($stage),
                'stages' => array_map(fn (StageView $stageView) => $this->normalizeStageView($stageView), $stages),
            ]
        );
    }

    #[Route(':{stageId}', name: 'api_stage', methods: ['GET'])]
    public function stage(StageViewRepository $repository, int $stageId): JsonResponse
    {
        $stage = $repository->getFullStage($stageId);
        $stages = $repository->findBy([], ['startDate' => 'ASC']);

        return $this->json(
            data: [
                'stage' => $this->normalizeStage($stage),
                'stages' => array_map(fn (StageView $stageView) => $this->normalizeStageView($stageView), $stages),
            ],
            context: ['groups' => ['stage:view']],
        );
    }

    /**
     * @return array{
     *     start_date: \DateTimeImmutable,
     *     end_date: \DateTimeImmutable,
     *     name: string,
     *     id: int,
     *     games: array{
     *          future: array<string, array{
     *                  id: int,
     *                  group_id: int,
     *                  group_name: string,
     *                  date: string,
     *                  time: string,
     *                  home_team: array{
     *                      id: int,
     *                      name: string,
     *                      logo: array{id: int, origin_name: string},
     *                      goals: int,
     *                  },
     *                  away_team: array{
     *                      id: int,
     *                      name: string,
     *                      logo: array{id: int, origin_name: string},
     *                      goals: int,
     *                  }
     *              }>,
     *           past: array<string, array{
     *                   id: int,
     *                   group_id: int,
     *                   group_name: string,
     *                   date: string,
     *                   time: string,
     *                   home_team: array{
     *                       id: int,
     *                       name: string,
     *                       logo: array{id: int, origin_name: string},
     *                       goals: int,
     *                   },
     *                   away_team: array{
     *                       id: int,
     *                       name: string,
     *                       logo: array{id: int, origin_name: string},
     *                       goals: int,
     *                   }
     *            }>
     *     }
     * }
     */
    private function normalizeStage(?StageView $stageView): array
    {
        $games = [
            self::GAMES_GROUP_FUTURE => [],
            self::GAMES_GROUP_PAST => [],
        ];

        if ($stageView) {
            $borderOfPast = new \DateTime('-2 hours');
            foreach ($stageView->getStage()->getGroups() as $stageGroup) {
                foreach ($stageGroup->getGames() as $game) {
                    $group = self::GAMES_GROUP_FUTURE;

                    if ($game->getDate() < $borderOfPast) {
                        $group = self::GAMES_GROUP_PAST;
                    }

                    $games[$group][$game->getDate()->format('Y-m-d H:i').' '.$game->getId()] = [
                        'id' => $game->getId(),
                        'group_id' => $stageGroup->getGroup()->getId(),
                        'group_name' => $stageGroup->getGroup()->getName(),
                        'date' => $game->getDate()->format('Y-m-d'),
                        'time' => $game->getDate()->format('H:i'),
                        'home_team' => [
                            'id' => $game->getHomeTeam()->getId(),
                            'name' => $game->getHomeTeam()->getName(),
                            'logo' => [
                                'id' => $game->getHomeTeam()->getLogo()?->getId(),
                                'origin_name' => $game->getHomeTeam()->getLogo()?->getOriginName(),
                            ],
                            'goals' => $game->getHomeGoals(),
                        ],
                        'away_team' => [
                            'id' => $game->getAwayTeam()->getId(),
                            'name' => $game->getAwayTeam()->getName(),
                            'logo' => [
                                'id' => $game->getAwayTeam()->getLogo()?->getId(),
                                'origin_name' => $game->getAwayTeam()->getLogo()?->getOriginName(),
                            ],
                            'goals' => $game->getAwayGoals(),
                        ],
                    ];
                }
            }

            ksort($games[self::GAMES_GROUP_FUTURE]);
            krsort($games[self::GAMES_GROUP_PAST]);
        }

        return [
            'start_date' => $stageView?->getStartDate(),
            'end_date' => $stageView?->getEndDate(),
            'name' => $stageView?->getStage()->getName(),
            'id' => $stageView?->getStage()->getId(),
            'games' => $games,
        ];
    }

    /**
     * @return array{id: int, short_name: string}
     */
    private function normalizeStageView(StageView $stageView): array
    {
        return [
            'id' => $stageView->getStage()->getId(),
            'short_name' => $stageView->getShortName(),
        ];
    }
}
