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
final class LikeNumeric extends Constraint
{
    public string $message = 'The {{ like_numeric }} is not like numeric.';
}
