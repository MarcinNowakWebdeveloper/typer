<?php

namespace App\Entity;

use App\Entity\PointCountingStrategy\Translation as PointCountingStrategyTranslation;
use App\Entity\User\UserGamePoints;
use App\Repository\PointCountingStrategyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PointCountingStrategyRepository::class)]
class PointCountingStrategy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['point_counting_strategy:read'])]
    private ?int $id = null;

    #[Groups(['point_counting_strategy:read'])]
    #[ORM\Column(length: 100)]
    private ?string $code = null;

    #[ORM\Column(type: Types::FLOAT)]
    private ?float $maxPointsPerGame = null;

    #[Groups(['point_counting_strategy:read'])]
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isDefault = false;

    /**
     * @var Collection<int, UserGamePoints>
     */
    #[ORM\OneToMany(targetEntity: UserGamePoints::class, mappedBy: 'pointCountingStrategy', orphanRemoval: true)]
    private Collection $userGamePoints;

    /**
     * @var Collection<int, PointCountingStrategyTranslation>
     */
    #[ORM\OneToMany(
        targetEntity: PointCountingStrategyTranslation::class,
        mappedBy: 'pointCountingStrategy',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $translations;

    public function __construct()
    {
        $this->userGamePoints = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

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
            $userGamePoint->setPointCountingStrategy($this);
        }

        return $this;
    }

    public function removeUserGamePoint(UserGamePoints $userGamePoint): static
    {
        if ($this->userGamePoints->removeElement($userGamePoint)) {
            // set the owning side to null (unless already changed)
            if ($userGamePoint->getPointCountingStrategy() === $this) {
                $userGamePoint->setPointCountingStrategy(null);
            }
        }

        return $this;
    }

    public function getMaxPointsPerGame(): ?float
    {
        return $this->maxPointsPerGame;
    }

    public function setMaxPointsPerGame(float $maxPointsPerGame): static
    {
        $this->maxPointsPerGame = $maxPointsPerGame;

        return $this;
    }

    public function isDefault(): ?bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): static
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * @return Collection<int, PointCountingStrategyTranslation>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(PointCountingStrategyTranslation $translation): static
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setPointCountingStrategy($this);
        }

        return $this;
    }

    public function removeTranslation(PointCountingStrategyTranslation $translation): static
    {
        if ($this->translations->removeElement($translation)) {
            // set the owning side to null (unless already changed)
            if ($translation->getPointCountingStrategy() === $this) {
                $translation->setPointCountingStrategy(null);
            }
        }

        return $this;
    }

    #[Groups(['point_counting_strategy:read'])]
    public function getName(): string
    {
        return $this->translations->first()->getName();
    }

    #[Groups(['point_counting_strategy:read'])]
    public function getDescription(): string
    {
        return $this->translations->first()->getDescription();
    }
}
