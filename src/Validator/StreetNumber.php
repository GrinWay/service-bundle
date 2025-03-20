<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class StreetNumber extends Constraint
{
    public string $message = 'The "{{ street_number }}" is not a street number string.';
}
