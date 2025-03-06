<?php

namespace GrinWay\Service\Test\Service\Messenger;

use GrinWay\Service\Entity\Test;
use GrinWay\Service\Service\Messenger\WorkerUtil as WorkerUtilAlias;

class WorkerUtil extends WorkerUtilAlias
{
    public function getTestAndRequiredAvoidRetryingIfNull(
        mixed     $entityId,
        ?array    $requirePropertyPaths = null,
        ?callable $entityNotFoundCallback = null,
        ?callable $entityPropertyPathNotFoundCallback = null,
    ): array
    {
        return $this->getEntityAndRequiredAvoidRetryingIfNull(
            fqcn: Test::class,
            entityId: $entityId,
            requirePropertyPaths: $requirePropertyPaths,
            entityNotFoundCallback: $entityNotFoundCallback,
            entityPropertyPathNotFoundCallback: $entityPropertyPathNotFoundCallback,
        );
    }
}
