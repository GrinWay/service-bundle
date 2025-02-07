<?php

namespace GrinWay\Service\EventListener\Doctrine;

use Carbon\FactoryImmutable;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Component\DependencyInjection\ServiceLocator;

class CreatedAtEventListener
{
    public function __construct(
        private readonly ServiceLocator $serviceLocator,
    )
    {
    }

    public function __invoke(
        PrePersistEventArgs $args,
    ): void
    {
        $ojb = $args->getObject();

        if (\method_exists($ojb, 'setCreatedAt')) {
            $carbonFactoryImmutable = $this->serviceLocator->get('carbonFI');
            \assert($carbonFactoryImmutable instanceof FactoryImmutable);

            $ojb->setCreatedAt($carbonFactoryImmutable->now(timezone: 'UTC'));
        }
    }
}
