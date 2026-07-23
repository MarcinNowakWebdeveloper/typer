<?php

namespace App\Service;

use App\Entity\Group;
use App\Repository\TeamRepository;

class GroupManager
{
    public function __construct(
        private TeamRepository $teamRepository,
    ) {
    }

    /**
     * @param array{name: string, teamIds: int[]} $data
     */
    public function updateFromArray(Group $group, array $data): void
    {
        $group->setName($data['name']);

        $teams = $this->teamRepository->findBy(['id' => array_unique(array_map('intval', $data['teamIds']))]);
        foreach ($group->getTeams()->toArray() as $team) {
            $group->removeTeam($team);
        }
        foreach ($teams as $team) {
            $group->addTeam($team);
        }
    }
}
