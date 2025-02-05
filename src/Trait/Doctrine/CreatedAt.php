<?php

namespace GrinWay\Service\Trait\Doctrine;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait CreatedAt
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    protected ?\DateTimeInterface $createdAt = null;

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt = null): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
