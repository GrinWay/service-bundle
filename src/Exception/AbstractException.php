<?php

namespace GrinWay\Service\Exception;

use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;

abstract class AbstractException extends \Exception
{
    abstract protected function exceptionGetMessage(): string;

    public function __construct(
        #[LanguageLevelTypeAware(['8.0' => 'int'], default: '')]                     $code = 0,
        #[LanguageLevelTypeAware(['8.0' => 'Throwable|null'], default: 'Throwable')] $previous = null
    )
    {
        $message = $this->exceptionGetMessage();

        parent::__construct(
            $message,
            $code,
            $previous,
        );
    }
}
