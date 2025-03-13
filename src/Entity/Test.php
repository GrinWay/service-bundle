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

    #[ORM\Column(type: Types::TEXT)]
    protected ?string $text = null;

    #[ORM\Column(nullable: true)]
    protected ?string $test = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    protected ?\DateTimeInterface $dateTime = null;

    #[ORM\Column(type: Types::DATEINTERVAL, nullable: true)]
    protected ?\DateInterval $dateinterval = null;

    #[ORM\OneToOne(inversedBy: 'test', cascade: ['persist'])]
    protected ?TestAssociation $association = null;

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(?\DateTimeInterface $dateTime = null): static
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getAssociation(): ?TestAssociation
    {
        return $this->association;
    }

    public function setAssociation(?TestAssociation $association): static
    {
        $this->association = $association;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function getTest(): ?string
    {
        return $this->test;
    }

    public function setTest(?string $test): void
    {
        $this->test = $test;
    }

    public function getDateinterval(): ?\DateInterval
    {
        return $this->dateinterval;
    }

    public function setDateinterval(?\DateInterval $dateinterval): void
    {
        $this->dateinterval = $dateinterval;
    }
}
