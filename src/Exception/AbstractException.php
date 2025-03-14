<?php

namespace GrinWay\Service\Exception;

use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;

abstract class AbstractException extends \Exception
{
    abstract protected function exceptionGetMessage(): string;

    public function __construct(
        array                                                                        $messageSprintfParameters = [],
        #[LanguageLevelTypeAware(['8.0' => 'int'], default: '')]                     $code = 0,
        #[LanguageLevelTypeAware(['8.0' => 'Throwable|null'], default: 'Throwable')] $previous = null
    )
    {
        if (empty($messageSprintfParameters)) {
            $message = $this->exceptionGetMessage();
        } else {
            $message = \sprintf($this->exceptionGetMessage(), ...$messageSprintfParameters);
        }

        parent::__construct(
            $message,
            $code,
            $previous,
        );
    }
}
