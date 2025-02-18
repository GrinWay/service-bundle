<?php

namespace GrinWay\Service\EventListener\Doctrine;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Ensures that UTC timezone only was used to persist/update date[time] to database
 *
 * https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/events.html#onflush
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class DateTimeToUtcBeforeToDatabaseEventListener
{
    public function __construct(
        private readonly ServiceLocator $serviceLocator,
    )
    {
    }

    public function __invoke(OnFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $this->currentDateTimeFieldForceToUtcTimezone($entity);
        }
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $this->currentDateTimeFieldForceToUtcTimezone($entity);
        }
    }

    private function currentDateTimeFieldForceToUtcTimezone(
        object $entity,
    ): void
    {
        $entityProperties = new \ReflectionObject($entity);
        foreach ($entityProperties->getProperties() as $entityProperty) {
            /** @var PropertyAccessorInterface $pa */
            $pa = $this->serviceLocator->get('property_accessor');

            $entityPropertyName = $entityProperty->getName();
            $entityPropertyValue = $entityProperty->getValue($entity);

            if ($entityPropertyValue instanceof \DateTimeInterface && $pa->isWritable($entity, $entityPropertyName)) {
                $pa->setValue(
                    $entity,
                    $entityPropertyName,
                    (clone $entityPropertyValue)->setTimezone(new \DateTimeZone('UTC')),
                );
            }
        }
    }
}
