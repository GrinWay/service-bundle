<?php

namespace GrinWay\Service\Exception;

use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;

abstract class AbstractException extends \Exception
{
    abstract protected function exceptionGetMessage(): string;

    public function __construct(
        array                                                                        $format = [],
        #[LanguageLevelTypeAware(['8.0' => 'int'], default: '')]                     $code = 0,
        #[LanguageLevelTypeAware(['8.0' => 'Throwable|null'], default: 'Throwable')] $previous = null
    )
    {
        if (empty($format)) {
            $message = $this->exceptionGetMessage();
        } else {
            $message = \sprintf($this->exceptionGetMessage(), $format);
        }

        parent::__construct(
            $message,
            $code,
            $previous,
        );
    }
}
