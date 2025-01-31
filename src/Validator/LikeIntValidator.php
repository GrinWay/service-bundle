<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use function Symfony\Component\String\u;

final class LikeIntValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var LikeInt $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $v = u($value)->match('~^(?<like_int>[0-9]+)$~')['like_int'] ?? null;
        if (null !== $v) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
