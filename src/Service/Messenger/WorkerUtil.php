<?php

namespace GrinWay\Service\Service\Messenger;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class WorkerUtil
{
    private EntityManagerInterface $em;
    private PropertyAccessorInterface $pa;

    #[Required]
    public function _setRequired_workerUtil(
        EntityManagerInterface    $em,
        PropertyAccessorInterface $pa,
    )
    {
        $this->em = $em;
        $this->pa = $pa;
    }

    /**
     * API
     *
     * It's a way of communication:
     *     Message minimized data (entity_id) -> Handler fetched data (got entity from message->entity_id)
     *
     * Used to get entity and its required not null data via property path.
     *
     * If data (origin entity id or found by repository entity or required property path data) is null
     * two things will happen:
     * 1) callback will get called
     *     if origin entity id or entity is null: $entityNotFoundCallback($entityId)
     *     if required property path data is null: $entityPropertyPathNotFoundCallback($requirePropertyPath)
     * 2) UnrecoverableMessageHandlingException will be thrown to avoid retrying
     *     because by design if you wanted some not null value from entity described with property path,
     *     but it's null, it will always be failing because it will always be null
     *     (needless to retry)
     *
     * @reutrn [
     *     0 => $entity,
     *     'required property path' => $value,
     *     'another required property path' => $itsValue,
     *     ...
     * ]
     */
    protected function getEntityAndRequiredAvoidRetryingIfNull(
        string    $fqcn,
        mixed     $entityId,
        ?array    $requirePropertyPaths = null,
        ?callable $entityNotFoundCallback = null,
        ?callable $entityPropertyPathNotFoundCallback = null,
    ): array
    {
        $data = [];

        $requirePropertyPaths ??= [];
        $entityNotFoundCallback ??= static fn(mixed $entityId) => true;
        $entityPropertyPathNotFoundCallback ??= static fn(string $requirePropertyPath) => true;

        $reflectionClass = new \ReflectionClass($fqcn);
        $reflectionClassShortName = $reflectionClass->getShortName();
        if (null === $entityId) {
            $entityNotFoundCallback($entityId);
            throw new UnrecoverableMessageHandlingException(
                \sprintf('%s id is null', $reflectionClassShortName),
            );
        }
        $entity = $this->em->find($fqcn, $entityId);
        if (null === $entity) {
            $entityNotFoundCallback($entityId);
            throw new UnrecoverableMessageHandlingException(
                \sprintf('%s is null', $reflectionClassShortName)
            );
        }

        foreach ($requirePropertyPaths as $requirePropertyPath) {
            $entityData = null;
            try {
                $entityData = $this->pa->getValue($entity, $requirePropertyPath);
            } catch (\Exception $exception) {
            }

            if (null === $entityData) {
                $entityPropertyPathNotFoundCallback($requirePropertyPath);
                throw new UnrecoverableMessageHandlingException(
                    \sprintf(
                        '%s by property path "%s" is null',
                        $reflectionClassShortName,
                        $requirePropertyPath,
                    ),
                );
            }
            $data[$requirePropertyPath] = $entityData;
        }

        $data[0] = $entity;

        return $data;
    }
}
