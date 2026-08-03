<?php

namespace App\Entity\User;

use App\Entity\Game;
use App\Entity\User;
use App\Exception\MismatchStrategiesException;
use App\Exception\PointsAlreadyAwardedException;
use App\Repository\User\UserGameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserGameRepository::class)]
class UserGame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['ranking:list'])]
    #[ORM\ManyToOne(inversedBy: 'usersGames')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Game $game = null;

    #[Groups(['ranking:list'])]
    #[ORM\Column(nullable: true)]
    private ?int $homeGoals = null;

    #[Groups(['ranking:list'])]
    #[ORM\Column(nullable: true)]
    private ?int $awayGoals = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, UserGamePoints>
     */
    #[ORM\OneToMany(targetEntity: UserGamePoints::class, mappedBy: 'userGame', orphanRemoval: true)]
    private Collection $userGamePoints;

    public function __construct()
    {
        $this->userGamePoints = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game = null): static
    {
        if (null === $game && !$this->userGamePoints->isEmpty()) {
            throw new PointsAlreadyAwardedException();
        }

        $this->game = $game;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, UserGamePoints>
     */
    public function getUserGamePoints(): Collection
    {
        return $this->userGamePoints;
    }

    public function addUserGamePoint(UserGamePoints $userGamePoint): static
    {
        if (!$this->userGamePoints->contains($userGamePoint)) {
            $this->userGamePoints->add($userGamePoint);
            $userGamePoint->setUserGame($this);
        }

        return $this;
    }

    public function removeUserGamePoint(UserGamePoints $userGamePoint): static
    {
        if ($this->userGamePoints->removeElement($userGamePoint)) {
            // set the owning side to null (unless already changed)
            if ($userGamePoint->getUserGame() === $this) {
                $userGamePoint->setUserGame(null);
            }
        }

        return $this;
    }

    #[Groups(['ranking:list'])]
    public function getPoints(): float
    {
        $strategyId = null;
        $points = 0;
        foreach ($this->userGamePoints as $userGamePoint) {
            if (null !== $strategyId && $strategyId !== $userGamePoint->getPointCountingStrategy()->getId()) {
                throw new MismatchStrategiesException('Strategy id mismatch');
            }
            $strategyId = $userGamePoint->getPointCountingStrategy()->getId();
            $points += $userGamePoint->getPoints();
        }

        return $points;
    }
}
