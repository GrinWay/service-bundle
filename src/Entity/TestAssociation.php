<?php

namespace GrinWay\Service\Entity;

use Doctrine\ORM\Mapping as ORM;
use GrinWay\Service\Repository\TestAssociationRepository;

#[ORM\Entity(repositoryClass: TestAssociationRepository::class)]
class TestAssociation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(mappedBy: 'association', cascade: ['persist', 'remove'])]
    private ?Test $test = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTest(): ?Test
    {
        return $this->test;
    }

    public function setTest(?Test $test): static
    {
        // unset the owning side of the relation if necessary
        if ($test === null && $this->test !== null) {
            $this->test->setAssociation(null);
        }

        // set the owning side of the relation if necessary
        if ($test !== null && $test->getAssociation() !== $this) {
            $test->setAssociation($this);
        }

        $this->test = $test;

        return $this;
    }
}
