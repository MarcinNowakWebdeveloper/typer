<?php

namespace App\Entity\PointCountingStrategy;

use App\Entity\PointCountingStrategy;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: '`point_counting_strategy_translation`')]
class Translation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['point_counting_strategy:read'])]
    private ?int $id = null;

    #[Groups(['point_counting_strategy:read'])]
    #[ORM\Column(length: 5)]
    private string $locale;

    #[Groups(['point_counting_strategy:read'])]
    #[ORM\Column(length: 255)]
    private string $name;

    #[Groups(['point_counting_strategy:read'])]
    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\ManyToOne(inversedBy: 'translations')]
    #[ORM\JoinColumn(nullable: true)]
    private ?PointCountingStrategy $pointCountingStrategy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getPointCountingStrategy(): PointCountingStrategy
    {
        return $this->pointCountingStrategy;
    }

    public function setPointCountingStrategy(?PointCountingStrategy $pointCountingStrategy): void
    {
        $this->pointCountingStrategy = $pointCountingStrategy;
    }
}
