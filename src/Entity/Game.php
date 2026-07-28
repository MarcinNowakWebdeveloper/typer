<?php

namespace App\Entity;

use App\Entity\Stage\Group;
use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[Groups([
        'admin:game:list',
        'admin:stage:group:view',
    ])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Group $stageGroup = null;

    #[Groups([
        'admin:game:list',
        'admin:stage:group:view',
    ])]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $homeTeam = null;

    #[Groups([
        'admin:game:list',
        'admin:stage:group:view',
    ])]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $awayTeam = null;

    #[Groups([
        'admin:game:list',
        'admin:stage:group:view',
    ])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[ORM\Column]
    #[ORM\JoinColumn(nullable: false)]
    private ?\DateTime $date = null;

    #[Groups([
        'admin:game:list',
        'admin:stage:group:view',
    ])]
    #[ORM\Column(nullable: true)]
    private ?int $homeGoals = null;

    #[Groups([
        'admin:game:list',
        'admin:stage:group:view',
    ])]
    #[ORM\Column(nullable: true)]
    private ?int $awayGoals = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getStageGroup(): ?Group
    {
        return $this->stageGroup;
    }

    public function setStageGroup(?Group $stageGroup = null): static
    {
        $this->stageGroup = $stageGroup;

        return $this;
    }

    public function getHomeTeam(): ?Team
    {
        return $this->homeTeam;
    }

    public function setHomeTeam(?Team $homeTeam): static
    {
        $this->homeTeam = $homeTeam;

        return $this;
    }

    public function getAwayTeam(): ?Team
    {
        return $this->awayTeam;
    }

    public function setAwayTeam(?Team $awayTeam): static
    {
        $this->awayTeam = $awayTeam;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getHomeGoals(): ?int
    {
        return $this->homeGoals;
    }

    public function setHomeGoals(?int $homeGoals): static
    {
        $this->homeGoals = $homeGoals;

        return $this;
    }

    public function getAwayGoals(): ?int
    {
        return $this->awayGoals;
    }

    public function setAwayGoals(?int $awayGoals): static
    {
        $this->awayGoals = $awayGoals;

        return $this;
    }

    #[Groups([
        'admin:game:list',
        'admin:stage:group:view',
    ])]
    public function getTime(): string
    {
        return $this->date->format('H:i');
    }
}
