<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[Groups([
        'admin:game:list',
        'admin:group:list',
        'admin:stage:list',
        'admin:stage:group:view',
        'team:list',
    ])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups([
        'admin:game:list',
        'admin:group:list',
        'admin:stage:group:view',
        'team:list',
    ])]
    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups([
        'admin:game:list',
        'admin:group:list',
        'admin:stage:group:view',
        'team:list',
    ])]
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?File $logo = null;

    #[Groups(['team:list'])]
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isJoker = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLogo(): ?File
    {
        return $this->logo;
    }

    public function setLogo(?File $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function isJoker(): bool
    {
        return $this->isJoker;
    }

    public function setIsJoker(bool $isJoker): void
    {
        $this->isJoker = $isJoker;
    }
}
