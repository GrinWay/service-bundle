<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use function Symfony\Component\String\u;

final class LikeIntValidator extends ConstraintValidator
{
    public function validate(mixed $likeInt, Constraint $constraint): void
    {
        /* @var LikeInt $constraint */

        if (null === $likeInt || '' === $likeInt) {
            return;
        }

        $v = u($likeInt)->match('~^(?<like_int>[0-9]+)$~')['like_int'] ?? null;
        if (null !== $v) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ like_int }}', $likeInt)
            ->addViolation();
    }
}
