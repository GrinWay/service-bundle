<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Street only
 * without street number
 * just street string
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class StreetWithoutNumber extends Constraint
{
    public string $message = 'The "{{ street }}" is not a street string.';
}
