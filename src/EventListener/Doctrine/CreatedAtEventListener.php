<?php

namespace GrinWay\Service\EventListener\Doctrine;

use Doctrine\ORM\Event\PrePersistEventArgs;

/**
 * Work together with "GrinWay\Service\Trait\Doctrine\CreatedAt"
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class CreatedAtEventListener
{
    public function __invoke(
        PrePersistEventArgs $args,
    ): void
    {
        $ojb = $args->getObject();

        if (\method_exists($ojb, $setter = 'setCreatedAt')) {
            $nowUtcImmutable = new \DateTimeImmutable(
                'now',
                new \DateTimeZone('UTC'),
            );
            [$ojb, $setter]($nowUtcImmutable);
        }
    }
}
