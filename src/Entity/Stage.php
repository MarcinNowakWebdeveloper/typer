<?php

namespace App\Entity;

use App\Entity\Stage\Group as StageGroup;
use App\Repository\StageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: StageRepository::class)]
class Stage
{
    use TimestampableEntity;

    #[Groups(['admin:stage:list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['admin:stage:list'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups([
        'admin:stage:list',
        'admin:stage:group:view',
    ])]
    #[ORM\Column(length: 15)]
    private ?string $shortName = null;

    /**
     * @var Collection<int, StageGroup>
     */
    #[Groups(['admin:stage:list'])]
    #[ORM\OneToMany(
        targetEntity: StageGroup::class,
        mappedBy: 'stage',
        cascade: ['persist', 'remove'],
        orphanRemoval: true)]
    private Collection $groups;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(?string $shortName): void
    {
        $this->shortName = $shortName;
    }

    /** @return Collection<int, StageGroup> */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /** @param Collection<int, StageGroup> $groups */
    public function setGroups(Collection $groups): static
    {
        $this->groups = $groups;

        return $this;
    }

    public function addGroup(StageGroup $group): static
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->setStage($this);
        }

        return $this;
    }

    public function removeGroup(StageGroup $group): static
    {
        if ($this->groups->removeElement($group)) {
            $group->setStage();
        }

        return $this;
    }
}
