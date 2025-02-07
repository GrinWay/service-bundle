<?php

namespace GrinWay\Service\Trait\Doctrine;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
trait UpdatedAt
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    protected ?\DateTimeInterface $updatedAt = null;

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt = null): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
