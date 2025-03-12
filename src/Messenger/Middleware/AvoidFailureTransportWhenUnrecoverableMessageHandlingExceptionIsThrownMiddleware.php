<?php

namespace GrinWay\Service\Messenger\Middleware;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Contracts\Service\Attribute\Required;

class AvoidFailureTransportWhenUnrecoverableMessageHandlingExceptionIsThrownMiddleware implements MiddlewareInterface
{
    protected LoggerInterface $logger;

    public function __construct(
        protected readonly bool $logUnrecoverableException = true,
        protected readonly bool $includeTractInUnrecoverableExceptionLog = true,
    )
    {
    }

    #[Required]
    public function _serRequired_avoidFailureTransportWhenUnrecoverableMessageHandlingExceptionIsThrownMiddleware(
        LoggerInterface $logger,
    ): void
    {
        $this->logger = $logger;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            return $stack->next()->handle($envelope, $stack);
        } catch (HandlerFailedException $exception) {
            $previousException = $exception;
            while (true) {
                $previousException = $previousException->getPrevious();
                if (null === $previousException) {
                    break;
                }
                if ($previousException instanceof UnrecoverableMessageHandlingException) {
                    if (true === $this->logUnrecoverableException) {
                        $context = [
                            'exception' => \get_debug_type($previousException),
                            'file' => $previousException->getFile(),
                            'line' => $previousException->getLine(),
                            'code' => $previousException->getCode(),
                        ];

                        if (true === $this->includeTractInUnrecoverableExceptionLog) {
                            $context['trace'] = $previousException->getTrace();
                        }

                        $this->logger->error($previousException->getMessage(), $context);
                    }
                    return $envelope;
                }
            }
            throw $exception;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
