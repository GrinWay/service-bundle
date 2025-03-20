<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use function Symfony\Component\String\u;

/**
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
final class StreetNumberValidator extends ConstraintValidator
{
    public function validate(mixed $streetNumber, Constraint $constraint): void
    {
        /* @var AbsolutePath $constraint */

        if (null === $streetNumber || '' === $streetNumber) {
            return;
        }

        if (!\is_scalar($streetNumber)) {
            $streetNumber = \get_debug_type($streetNumber);
        } else {
            if (null !== (u($streetNumber)->match('~^(?<street_number>[0-9\s.,\/\\\_\-]+)$~')['street_number'] ?? null)) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ street_number }}', $streetNumber)
            ->addViolation();
    }
}
