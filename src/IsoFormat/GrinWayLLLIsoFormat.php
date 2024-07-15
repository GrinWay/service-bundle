<?php

namespace GrinWay\Service\IsoFormat;

use GrinWay\Service\Contracts\GrinWayIsoFormat;

class GrinWayLLLIsoFormat implements GrinWayIsoFormat
{
    public static function get(): string
    {
        return 'dddd, MMMM D, YYYY h:mm:ss A';
    }
}
