<?php

namespace App\Service;

use App\Entity\Game;
use App\Exception\InvalidDataException;
use App\Repository\Stage\GroupRepository;
use App\Repository\TeamRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class GameManager
{
    public function __construct(
        private GroupRepository $groupRepository,
        private TeamRepository $teamRepository,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @param array{
     *      stageGroupId: int,
     *      date: string,
     *      time: string,
     *      homeTeamId: int,
     *      awayTeamId: int,
     *      homeGoals: ?int,
     *      awayGoals: ?int
     *  } $data
     */
    public function updateFromArray(Game $game, array $data): void
    {
        $stageGroup = $this->groupRepository->find($data['stageGroupId']);
        if (!$stageGroup) {
            $objectName = $this->translator->trans('game.stageGroupId', [], 'entities');
            $message = $this->translator->trans('form.notFound', ['{object}' => $objectName], 'errors');

            throw new InvalidDataException(message: $message, code: Response::HTTP_NOT_FOUND);
        }

        $homeTeam = $this->teamRepository->find($data['homeTeamId']);
        if (!$homeTeam) {
            $objectName = $this->translator->trans('game.homeTeamId', [], 'entities');
            $message = $this->translator->trans('form.notFound', ['{object}' => $objectName], 'errors');

            throw new InvalidDataException(message: $message, code: Response::HTTP_NOT_FOUND);
        }

        $awayTeam = $this->teamRepository->find($data['awayTeamId']);
        if (!$awayTeam) {
            $objectName = $this->translator->trans('game.awayTeamId', [], 'entities');
            $message = $this->translator->trans('form.notFound', ['{object}' => $objectName], 'errors');

            throw new InvalidDataException(message: $message, code: Response::HTTP_NOT_FOUND);
        }

        $game->setDate(new \DateTime($data['date'].' '.$data['time']));
        $game->setStageGroup($stageGroup);
        $game->setHomeTeam($homeTeam);
        $game->setAwayTeam($awayTeam);
        $game->setHomeGoals($data['homeGoals']);
        $game->setAwayGoals($data['awayGoals']);
    }
}
