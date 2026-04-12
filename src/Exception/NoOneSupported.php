<?php

namespace GrinWay\Service\Exception;

class NoOneSupported extends AbstractException
{
    protected function exceptionGetMessage(): string
    {
        return 'No one "%s" is supported.';
    }
}
