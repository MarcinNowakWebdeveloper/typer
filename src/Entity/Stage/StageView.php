<?php

namespace App\Entity\Stage;

use App\Entity\Stage;
use App\Repository\Stage\StageViewRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StageViewRepository::class, readOnly: true)]
#[ORM\Table(name: 'stage_view')]
class StageView
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Stage::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private Stage $stage;

    #[ORM\Column(name: 'short_name', type: 'string')]
    private ?string $shortName = null;
    #[ORM\Column(name: 'start_date', type: 'datetime_immutable')]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(name: 'end_date', type: 'datetime_immutable')]
    private \DateTimeImmutable $endDate;

    public function getStage(): Stage
    {
        return $this->stage;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }
}
