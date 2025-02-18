<?php

namespace GrinWay\Service\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use GrinWay\Service\Repository\TestRepository;
use GrinWay\Service\Trait\Doctrine\CreatedAt;
use GrinWay\Service\Trait\Doctrine\UpdatedAt;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test
{
    use CreatedAt, UpdatedAt;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    protected ?\DateTimeInterface $dateTime = null;

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(?\DateTimeInterface $dateTime = null): static
    {
        $this->dateTime = $dateTime;

        return $this;
    }
}
