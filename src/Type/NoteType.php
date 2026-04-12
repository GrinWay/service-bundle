<?php

namespace GrinWay\Service\Type;

/**
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class NoteType
{
    public const NOTICE           = 'notice';
    public const WARNING          = 'warning';
    public const ERROR            = 'error';

    public const TYPES = [
        'NOTICE'         => self::NOTICE,
        'WARNING'        => self::WARNING,
        'ERROR'          => self::ERROR,
    ];

    public const SNAKE_KEYS_TYPES = [
        'notice'         => self::NOTICE,
        'warning'        => self::WARNING,
        'error'          => self::ERROR,
    ];
}
