<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use function Symfony\Component\String\u;

/**
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
final class StreetValidator extends ConstraintValidator
{
    public function validate(mixed $street, Constraint $constraint): void
    {
        if (null === $street || '' === $street) {
            return;
        }

        if (!\is_scalar($street)) {
            $street = \get_debug_type($street);
        } else {
            if (null !== (u($street)->match('~^(?<street>[a-z\(\p{Cyrillic}\)0-9 \/\\\_\-,.]+)$~ui')['street'] ?? null)) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ street }}', $street)
            ->addViolation();
    }
}
