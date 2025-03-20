<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use function Symfony\Component\String\u;

/**
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
final class LikeIntValidator extends ConstraintValidator
{
    public function validate(mixed $likeInt, Constraint $constraint): void
    {
        /* @var LikeInt $constraint */

        if (null === $likeInt || '' === $likeInt) {
            return;
        }

        if (!\is_scalar($likeInt)) {
            $likeInt = \get_debug_type($likeInt);
        } else {
            $likeInt = (string)$likeInt;
            $v = u($likeInt)->match('~^(?<like_int>[0-9]+)$~')['like_int'] ?? null;
            if (null !== $v) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ like_int }}', $likeInt)
            ->addViolation();
    }
}
