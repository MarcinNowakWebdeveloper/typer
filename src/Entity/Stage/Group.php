<?php

namespace App\Entity\Stage;

use App\Entity\Stage;
use App\Repository\Stage\GroupRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: 'stage_group')]
class Group
{
    #[Groups(['admin:stage:list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['admin:stage:list'])]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Group::class)]
    private \App\Entity\Group $group;

    #[ORM\ManyToOne(targetEntity: Stage::class, inversedBy: 'groups')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Stage $stage = null;

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
    }
}
