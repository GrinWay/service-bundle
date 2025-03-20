<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Filesystem\Path;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
final class AbsolutePathValidator extends ConstraintValidator
{
    public function validate(mixed $path, Constraint $constraint): void
    {
        /* @var AbsolutePath $constraint */

        if (null === $path || '' === $path) {
            return;
        }

        if (!\is_scalar($path)) {
            $path = \get_debug_type($path);
        } else {
            $path = (string)$path;
            if (Path::isAbsolute($path)) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ path }}', $path)
            ->addViolation();
    }
}
