<?php

namespace GrinWay\Service\Trait\Doctrine;

use Doctrine\ORM\Mapping as ORM;

trait Active
{
    #[ORM\Column]
    protected ?bool $active = false;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }
}
