<?php

namespace GrinWay\Service\Exception\Fixer;

/**
 * Is thrown when there is no 'base' key in the response payload from Fixer API
 * If false === fixerApiRequestPayload['success'], base won't be supplied
 *
 * It means that the response to the Fixer API was not success in a certain meaning:
 * 1. Maybe you forgot or not included you 'access_key' 'GET' query parameter, or it has been expired, or even incorrect
 * 2. You reached monthly possible requests (because of this, current bundle uses the symfony cache system)
 *
 * At any rate you can catch it in your code
 *
 * https://fixer.io/documentation
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class NoBaseFixerException extends AbstractFixerException
{
    protected function exceptionGetMessage(): string
    {
        return 'There is no base currency form the Fixer API, conversion is not possible';
    }
}
