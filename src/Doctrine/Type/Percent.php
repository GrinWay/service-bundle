<?php

namespace GrinWay\Service\Doctrine\Type;

use GrinWay\Service\Validator\LikeNumeric;
use Symfony\Component\Validator\Validation;

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
    public function __construct(private ?string $percent = null)
    {
        Validation::createCallable(new LikeNumeric())($this->percent);
    }

    public function getPercent(): ?string
    {
        return $this->percent;
    }

    public function setPercent(?string $percent): static
    {
        $this->percent = $percent;

        return $this;
    }

    public function __toString(): string
    {
        return (string)$this->percent;
    }
}
