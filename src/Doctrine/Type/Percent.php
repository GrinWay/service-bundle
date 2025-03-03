<?php

namespace GrinWay\Service\Doctrine\Type;

/**
 * Percent object for "percent" dbal type
 *
 * Usage:
 * #[ORM\Column(type: PercentType::NAME)]
 * private ?Percent $percent = null;
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class Percent implements \Stringable
{
    public function __construct(
        private string|float|null $percent = null,
    )
    {
    }

    public function getPercent(): string|float|null
    {
        return $this->percent;
    }

    public function setPercent(string|float|null $percent): static
    {
        $this->percent = $percent;

        return $this;
    }

    public function toFloat(): float
    {
        if (empty($this->percent)) {
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
