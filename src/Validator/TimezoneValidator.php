<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use function Symfony\Component\String\u;

final class TimezoneValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (\in_array($value, \timezone_identifiers_list())) {
            return;
        }

        if (null !== (u($value)->match('~^(?<timezone>[+\-][0-9]{2}[:][0-9]{2})$~i')['timezone'] ?? null)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ timezone }}', $value)
            ->addViolation();
    }
}
