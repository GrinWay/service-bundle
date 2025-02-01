<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class LikeNumeric extends Constraint
{
    public string $message = 'The {{ like_numeric }} is not like numeric.';
}
