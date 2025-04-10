<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use function Symfony\Component\String\u;

final class PhoneNumberValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (null !== (u($value)->match('~^(?<phone_number>\+?[0-9 \-\(\)]+)$~')['phone_number'] ?? null)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ phone_number }}', $value)
            ->addViolation();
    }
}
