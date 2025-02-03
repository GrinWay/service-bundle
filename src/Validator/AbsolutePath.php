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
class AbsolutePath extends Constraint
{
    public string $message = 'The path "{{ path }}" is not an absolute one.';
}
