<?php

namespace GrinWay\Service\EventListener\Doctrine;

use Carbon\FactoryImmutable;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ServiceLocator;

class UpdatedAtEventListener
{
    public function __construct(
        private readonly ServiceLocator $serviceLocator,
    )
    {
    }

    public function __invoke(
        PreUpdateEventArgs $args,
    ): void
    {
        $ojb = $args->getObject();

        if (\method_exists($ojb, 'setUpdatedAt')) {
            $carbonFactoryImmutable = $this->serviceLocator->get('carbonFI');
            \assert($carbonFactoryImmutable instanceof FactoryImmutable);

            $ojb->setUpdatedAt($carbonFactoryImmutable->now(timezone: 'UTC'));
        }
    }
}
