<?php

namespace App\Entity\User;

use App\Entity\PointCountingStrategy;
use App\Repository\User\UserGamePointsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserGamePointsRepository::class)]
class UserGamePoints
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    #[Groups(['ranking:list'])]
    private ?float $points = null;

    #[ORM\ManyToOne(inversedBy: 'userGamePoints')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserGame $userGame = null;

    #[ORM\ManyToOne(inversedBy: 'userGamePoints')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PointCountingStrategy $pointCountingStrategy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPoints(): ?float
    {
        return $this->points;
    }

    public function setPoints(float $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getUserGame(): ?UserGame
    {
        return $this->userGame;
    }

    public function setUserGame(?UserGame $userGame): static
    {
        $this->userGame = $userGame;

        return $this;
    }

    public function getPointCountingStrategy(): ?PointCountingStrategy
    {
        return $this->pointCountingStrategy;
    }

    public function setPointCountingStrategy(?PointCountingStrategy $pointCountingStrategy): static
    {
        $this->pointCountingStrategy = $pointCountingStrategy;

        return $this;
    }
}
