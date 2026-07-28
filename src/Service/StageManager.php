<?php

namespace App\Service;

use App\Entity\Stage;
use App\Entity\Stage\Group as StageGroupEntity;
use App\Repository\GroupRepository;

class StageManager
{
    public function __construct(
        private GroupRepository $groupRepository,
    ) {
    }

    /**
     * @param array{name: string, shortName: string, groupIds: int[]} $data
     */
    public function updateFromArray(Stage $stage, array $data): void
    {
        $stage->setName($data['name']);
        $stage->setShortName($data['shortName']);

        $groups = $this->groupRepository->findAndIndexById($data['groupIds']);
        foreach ($stage->getGroups() as $group) {
            if (!in_array($group->getGroup()->getId(), $data['groupIds'])) {
                $stage->removeGroup($group);
            } else {
                unset($groups[$group->getGroup()->getId()]);
            }
        }

        foreach ($groups as $group) {
            $stageGroup = new StageGroupEntity();
            $stageGroup->setGroup($group);
            $stage->addGroup($stageGroup);
        }
    }
}
