<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class LikeInt extends Constraint
{
    public string $message = 'It is not like int: {{ int }}.';
}
