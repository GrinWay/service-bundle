<?php

namespace GrinWay\Service\Exception\Fixer;

/**
 * Is thrown when there is no 'success' key in the response payload from Fixer API
 * Fixer always returns 'success' key, with bool true or false
 *
 * Probably you will never get this exception until API behaviour changes
 *
 * At any rate you can catch it in your code
 *
 * https://fixer.io/documentation
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class NotSuccessFixerException extends AbstractFixerException
{
    protected function exceptionGetMessage(): string
    {
        return 'Request to the fixer API was not successful';
    }
}
