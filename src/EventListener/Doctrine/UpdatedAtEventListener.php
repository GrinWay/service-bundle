<?php

namespace GrinWay\Service\EventListener\Doctrine;

use Doctrine\ORM\Event\PreUpdateEventArgs;

/**
 * Work together with "GrinWay\Service\Trait\Doctrine\UpdatedAt"
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class UpdatedAtEventListener
{
    public function __invoke(
        PreUpdateEventArgs $args,
    ): void
    {
        $ojb = $args->getObject();

        if (\method_exists($ojb, $setter = 'setUpdatedAt')) {
            $nowUtcImmutable = new \DateTimeImmutable(
                'now',
                new \DateTimeZone('UTC'),
            );
            [$ojb, $setter]($nowUtcImmutable);
        }
    }
}
