<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use function Symfony\Component\String\u;

/**
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
final class EntranceValidator extends ConstraintValidator
{
    public function validate(mixed $entrance, Constraint $constraint): void
    {
        if (null === $entrance || '' === $entrance) {
            return;
        }

        if (!\is_scalar($entrance)) {
            $entrance = \get_debug_type($entrance);
        } else {
            if (null !== (u($entrance)->match('~^(?<entrance>[0-9\s]+)$~ui')['entrance'] ?? null)) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ entrance }}', $entrance)
            ->addViolation();
    }
}
