<?php

namespace GrinWay\Service\Contract\Doctrine;

use GrinWay\Service\GrinWayServiceBundle;

interface DoctrineEventListenerInterface
{
    public const TAG = GrinWayServiceBundle::BUNDLE_PREFIX . 'doctrine.event_listener';
}
