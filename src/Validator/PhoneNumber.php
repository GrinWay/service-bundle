<?php

namespace GrinWay\Service\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class PhoneNumber extends Constraint
{
    public string $message = 'The "{{ phone_number }}" is not a valid phone number string.';
}
