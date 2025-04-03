<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class Timezone extends Constraint
{
    public string $message = 'The {{ timezone }} is not a valid timezone string.';
}
