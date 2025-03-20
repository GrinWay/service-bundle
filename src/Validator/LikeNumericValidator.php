<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
final class LikeNumericValidator extends ConstraintValidator
{
    public function validate(mixed $likeNumeric, Constraint $constraint): void
    {
        /* @var LikeNumeric $constraint */

        if (null === $likeNumeric || '' === $likeNumeric) {
            return;
        }

        if (!\is_scalar($likeNumeric)) {
            $likeNumeric = \get_debug_type($likeNumeric);
        } else {
            if (\is_numeric($likeNumeric)) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ like_numeric }}', $likeNumeric)
            ->addViolation();
    }
}
