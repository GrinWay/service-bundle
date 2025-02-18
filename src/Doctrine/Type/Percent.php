<?php

namespace GrinWay\Service\Doctrine\Type;

/**
 * Percent object for "percent" dbal type
 *
 * Usage:
 * #[ORM\Column(type: 'percent')]
 * private ?Percent $percent = null;
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class Percent implements \Stringable
{
    public function __construct(
        private ?float $percent = null,
    )
    {
    }

    public function getPercent(): ?float
    {
        return $this->percent;
    }

    public function setPercent(?float $percent): static
    {
        $this->percent = $percent;

        return $this;
    }

    public function toFloat(): float
    {
        if (null === $this->percent) {
            $percent = 0.;
        } else {
            $percent = $this->percent;
        }

        return $percent;
    }

    public function __toString(): string
    {
        return (string)$this->percent;
    }
}
