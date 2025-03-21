<?php

namespace GrinWay\Service\Trait\Doctrine;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
trait Priority
{
    #[ORM\Column(type: Types::SMALLINT)]
    protected ?int $priority = 0;

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }
}
