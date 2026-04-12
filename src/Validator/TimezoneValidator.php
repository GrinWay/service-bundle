<?php

namespace GrinWay\Service\Validator;

use Carbon\Carbon;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

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

        $valid = true;
        try {
            Carbon::now('UTC')->timezone($value);
        } catch (\Exception) {
            $valid = false;
        }
        if (true === $valid) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ timezone }}', $value)
            ->addViolation();
    }
}
