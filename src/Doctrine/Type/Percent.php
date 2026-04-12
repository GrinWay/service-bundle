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
        private string|float|int|null $percent = null,
    )
    {
    }

    public function getPercent(): string|float|int|null
    {
        return $this->percent;
    }

    public function setPercent(string|float|int|null $percent): static
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

        return (float)$percent;
    }

    public function __toString(): string
    {
        return (string)$this->percent;
    }

    /**
     * Round to precision 2 (drops 3rd number after the dot)
     */
    public function getPercentOf(float $number): float {
        $percent = $number * ($this->toFloat() / 100);
        return ((int)(\round($percent, 3) * 100)) / 100;
    }
}
