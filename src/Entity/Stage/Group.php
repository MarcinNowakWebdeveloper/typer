<?php

namespace App\Entity\Stage;

use App\Entity\Game;
use App\Entity\Stage;
use App\Repository\Stage\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: 'stage_group')]
class Group
{
    #[Groups([
        'admin:stage:list',
        'admin:stage:group:view',
    ])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups([
        'admin:stage:list',
        'admin:stage:group:view',
    ])]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Group::class)]
    private \App\Entity\Group $group;

    #[Groups(['admin:stage:group:view'])]
    #[ORM\ManyToOne(targetEntity: Stage::class, inversedBy: 'groups')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Stage $stage = null;

    /**
     * @var Collection<int, Game>
     */
    #[Groups(['admin:stage:group:view'])]
    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'stageGroup', orphanRemoval: true)]
    #[ORM\OrderBy(['date' => 'ASC'])]
    private Collection $games;

    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getGroup(): \App\Entity\Group
    {
        return $this->group;
    }

    public function setGroup(\App\Entity\Group $group): void
    {
        $this->group = $group;
    }

    public function getStage(): Stage
    {
        return $this->stage;
    }

    public function setStage(?Stage $stage = null): void
    {
        $this->stage = $stage;
        if (null === $stage) {
            foreach ($this->games as $game) {
                $this->removeGame($game);
            }
        }
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->setStageGroup($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->games->removeElement($game)) {
            if ($game->getStageGroup() === $this) {
                $game->setStageGroup();
            }
        }

        return $this;
    }
}
