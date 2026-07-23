<?php

namespace App\Service;

use App\Entity\File;
use App\Entity\Team;

class TeamManager
{
    public function __construct(
        private FileService $fileService,
    ) {
    }

    /**
     * @param array{ name: string, image: ?File} $data
     */
    public function updateFromArray(Team $team, array $data): void
    {
        if ($data['image']) {
            if ($team->getLogo()) {
                $this->fileService->remove($team->getLogo());
            }
            $team->setLogo($data['image']);
        }

        $team->setName($data['name']);
    }
}
