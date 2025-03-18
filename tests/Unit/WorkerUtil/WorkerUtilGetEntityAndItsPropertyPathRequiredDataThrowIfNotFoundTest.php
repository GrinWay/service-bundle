<?php

namespace GrinWay\Service\Tests\Unit\WorkerUtil;

use GrinWay\Service\Factory\TestFactory;
use GrinWay\Service\Service\Messenger\WorkerUtil as WorkerUtilAlias;
use GrinWay\Service\Test\Entity\TestAssociation;
use GrinWay\Service\Test\Service\Messenger\WorkerUtil as TestWorkerUtil;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[CoversClass(WorkerUtilAlias::class)]
class WorkerUtilGetEntityAndItsPropertyPathRequiredDataThrowIfNotFoundTest extends AbstractWorkerUtilTestCase
{
    public function testThrowsUnrecoverableMessageHandlingExceptionOnNullEntityId()
    {
        /** @var TestWorkerUtil $workerUtil */
        $workerUtil = self::$workerUtil;

        $entityId = null;
        $require = [];

        $callbackWasCalled = false;
        $objectNotFoundCallback = static function (mixed $objectId) use (&$callbackWasCalled) {
            $callbackWasCalled = $objectId;
        };
        $this->expectException(UnrecoverableMessageHandlingException::class);
        $this->expectExceptionMessage('Test id is null');
        $workerUtil->getTestAndRequiredAvoidRetryingIfNull(
            entityId: $entityId,
            requirePropertyPaths: $require,
            entityNotFoundCallback: $objectNotFoundCallback,
        );
        $this->assertSame($entityId, $callbackWasCalled);
    }

    public function testThrowsUnrecoverableMessageHandlingExceptionOnNullEntity()
    {
        /** @var TestWorkerUtil $workerUtil */
        $workerUtil = self::$workerUtil;

        $entityId = 3;
        $require = [];

        $callbackWasCalled = null;
        $objectNotFoundCallback = static function (mixed $objectId) use (&$callbackWasCalled) {
            $callbackWasCalled = $objectId;
        };
        $this->expectException(UnrecoverableMessageHandlingException::class);
        $this->expectExceptionMessage('Test is null');
        $workerUtil->getTestAndRequiredAvoidRetryingIfNull(
            entityId: $entityId,
            requirePropertyPaths: $require,
            entityNotFoundCallback: $objectNotFoundCallback,
        );
        $this->assertSame($entityId, $callbackWasCalled);
    }

    public function testThrowsUnrecoverableMessageHandlingExceptionOnRequiredNonExistentEntityPropertyPath()
    {
        /** @var TestWorkerUtil $workerUtil */
        $workerUtil = self::$workerUtil;

        $entityId = 1;
        $require = [
            'nonExistentProperty',
        ];

        $callbackWasCalled = null;
        $objectRelatedDataNotFoundCallback = static function (string $requirePropertyPath) use (&$callbackWasCalled) {
            $callbackWasCalled = $requirePropertyPath;
        };
        $this->expectException(UnrecoverableMessageHandlingException::class);
        $this->expectExceptionMessage('Test by property path "nonExistentProperty" is null');
        $workerUtil->getTestAndRequiredAvoidRetryingIfNull(
            entityId: $entityId,
            requirePropertyPaths: $require,
            entityPropertyPathNotFoundCallback: $objectRelatedDataNotFoundCallback,
        );
        $this->assertSame('nonExistentProperty', $callbackWasCalled);
    }

    public function testThrowsUnrecoverableMessageHandlingExceptionOnRequiredEntityPropertyPathWithNullValue()
    {
        /** @var TestWorkerUtil $workerUtil */
        $workerUtil = self::$workerUtil;

        $entityId = 2; // with null test
        $require = [
            'test',
        ];

        $callbackWasCalled = null;
        $objectRelatedDataNotFoundCallback = static function (string $requirePropertyPath) use (&$callbackWasCalled) {
            $callbackWasCalled = $requirePropertyPath;
        };
        $this->expectException(UnrecoverableMessageHandlingException::class);
        $this->expectExceptionMessage('Test by property path "test" is null');
        $workerUtil->getTestAndRequiredAvoidRetryingIfNull(
            entityId: $entityId,
            requirePropertyPaths: $require,
            entityPropertyPathNotFoundCallback: $objectRelatedDataNotFoundCallback,
        );
        $this->assertSame('test', $callbackWasCalled);
    }

    public function testThrowsUnrecoverableMessageHandlingExceptionOnRequiredNonExistentEntityPropertyNestedPath()
    {
        /** @var TestWorkerUtil $workerUtil */
        $workerUtil = self::$workerUtil;

        $entityId = 1;
        $require = [
            'nonExistentProperty.name',
        ];

        $callbackWasCalled = null;
        $objectRelatedDataNotFoundCallback = static function (string $requirePropertyPath) use (&$callbackWasCalled) {
            $callbackWasCalled = $requirePropertyPath;
        };
        $this->expectException(UnrecoverableMessageHandlingException::class);
        $this->expectExceptionMessage('Test by property path "nonExistentProperty.name" is null');
        $workerUtil->getTestAndRequiredAvoidRetryingIfNull(
            entityId: $entityId,
            requirePropertyPaths: $require,
            entityPropertyPathNotFoundCallback: $objectRelatedDataNotFoundCallback,
        );
        $this->assertSame('nonExistentProperty.name', $callbackWasCalled);
    }

    public function testThrowsUnrecoverableMessageHandlingExceptionOnRequiredExistentEntityPropertyPathWithNullValue()
    {
        /** @var TestWorkerUtil $workerUtil */
        $workerUtil = self::$workerUtil;

        $entityId = 1;
        $testProxy = TestFactory::find($entityId);
        $testProxy->setText(null);
        $require = [
            'text',
        ];

        $callbackWasCalled = null;
        $objectRelatedDataNotFoundCallback = static function (string $requirePropertyPath) use (&$callbackWasCalled) {
            $callbackWasCalled = $requirePropertyPath;
        };
        $this->expectException(UnrecoverableMessageHandlingException::class);
        $this->expectExceptionMessage('Test by property path "text" is null');
        $workerUtil->getTestAndRequiredAvoidRetryingIfNull(
            entityId: $entityId,
            requirePropertyPaths: $require,
            entityPropertyPathNotFoundCallback: $objectRelatedDataNotFoundCallback,
        );
        $this->assertSame('text', $callbackWasCalled);
    }

    public function testThrowsUnrecoverableMessageHandlingExceptionOnRequiredNotValidPropertyPath()
    {
        /** @var TestWorkerUtil $workerUtil */
        $workerUtil = self::$workerUtil;

        $entityId = 1;
        $require = [
            'text', // it is NOT an association
            'text.id', // to require its id
        ];

        $callbackWasCalled = null;
        $objectRelatedDataNotFoundCallback = static function (string $requirePropertyPath) use (&$callbackWasCalled) {
            $callbackWasCalled = $requirePropertyPath;
        };
        $this->expectException(UnrecoverableMessageHandlingException::class);
        $this->expectExceptionMessage('Test by property path "text.id" is null');
        $workerUtil->getTestAndRequiredAvoidRetryingIfNull(
            entityId: $entityId,
            requirePropertyPaths: $require,
            entityPropertyPathNotFoundCallback: $objectRelatedDataNotFoundCallback,
        );
        $this->assertSame('text.id', $callbackWasCalled);
    }

    public function testThrowsUnrecoverableMessageHandlingExceptionOnRequiredNullAssociation()
    {
        /** @var TestWorkerUtil $workerUtil */
        $workerUtil = self::$workerUtil;

        $entityId = 2; // with null association
        $require = [
            'association',
            'association.id',
        ];

        $callbackWasCalled = null;
        $objectRelatedDataNotFoundCallback = static function (string $requirePropertyPath) use (&$callbackWasCalled) {
            $callbackWasCalled = $requirePropertyPath;
        };
        $this->expectException(UnrecoverableMessageHandlingException::class);
        $this->expectExceptionMessage('Test by property path "association" is null');
        $workerUtil->getTestAndRequiredAvoidRetryingIfNull(
            entityId: $entityId,
            requirePropertyPaths: $require,
            entityPropertyPathNotFoundCallback: $objectRelatedDataNotFoundCallback,
        );
        $this->assertSame('association', $callbackWasCalled);
    }

    public function testThrowsUnrecoverableMessageHandlingExceptionOnRequiredFirstNullAssociationIdBecauseFirstMatchesFirst()
    {
        /** @var TestWorkerUtil $workerUtil */
        $workerUtil = self::$workerUtil;

        $entityId = 2; // with null association
        $require = [
            'association.id',
            'association',
        ];

        $callbackWasCalled = null;
        $objectRelatedDataNotFoundCallback = static function (string $requirePropertyPath) use (&$callbackWasCalled) {
            $callbackWasCalled = $requirePropertyPath;
        };
        $this->expectException(UnrecoverableMessageHandlingException::class);
        $this->expectExceptionMessage('Test by property path "association.id" is null');
        $workerUtil->getTestAndRequiredAvoidRetryingIfNull(
            entityId: $entityId,
            requirePropertyPaths: $require,
            entityPropertyPathNotFoundCallback: $objectRelatedDataNotFoundCallback,
        );
        $this->assertSame('association.id', $callbackWasCalled);
    }

    public function testThrowsUnrecoverableMessageHandlingExceptionOnRequiredAssociationIdPropertyPathWithNullValue()
    {
        /** @var TestWorkerUtil $workerUtil */
        $workerUtil = self::$workerUtil;

        // id 3
        $entity = TestFactory::createOne();
        $entity->setAssociation(new TestAssociation()); // there is an association with NULL id!

        $entityId = 3;
        $require = [
            'association',
            'association.id',
        ];

        $callbackWasCalled = null;
        $objectRelatedDataNotFoundCallback = static function (string $requirePropertyPath) use (&$callbackWasCalled) {
            $callbackWasCalled = $requirePropertyPath;
        };
        $this->expectException(UnrecoverableMessageHandlingException::class);
        $this->expectExceptionMessage('Test by property path "association.id" is null');
        $workerUtil->getTestAndRequiredAvoidRetryingIfNull(
            entityId: $entityId,
            requirePropertyPaths: $require,
            entityPropertyPathNotFoundCallback: $objectRelatedDataNotFoundCallback,
        );
        $this->assertSame('association.id', $callbackWasCalled);
    }

    public function testFetchedEntityAndRequiredPropertyPath()
    {
        /** @var TestWorkerUtil $workerUtil */
        $workerUtil = self::$workerUtil;

        $entityId = 1;
        $require = [
            'text',
        ];

        [
            0 => $entity, // key is the lowercase short name of entity
            'text' => $text,
        ] = $workerUtil->getTestAndRequiredAvoidRetryingIfNull(
            entityId: $entityId,
            requirePropertyPaths: $require,
        );

        $this->assertSame($entityId, $entity->getId());
        $this->assertSame('TEST TEXT', $text);
    }

    public function testFetchedEntityAndRequiredAssociationPropertyPath()
    {
        /** @var TestWorkerUtil $workerUtil */
        $workerUtil = self::$workerUtil;

        $entityId = 1;
        $require = [
            'association',
        ];

        [
            0 => $entity, // key is the lowercase short name of entity
            'association' => $association,
        ] = $workerUtil->getTestAndRequiredAvoidRetryingIfNull(
            entityId: $entityId,
            requirePropertyPaths: $require,
        );

        $this->assertSame($entityId, $entity->getId());
        $this->assertSame(
            TestFactory::find($entityId)->getAssociation()->getId(),
            $association->getId(),
        );
    }

    public function testFetchedEntityAndRequiredAssociationPropertyPathAndAssociationIdPropertyPath()
    {
        /** @var TestWorkerUtil $workerUtil */
        $workerUtil = self::$workerUtil;

        $entityId = 1;
        $require = [
            'association',
            'association.id',
        ];

        [
            0 => $entity, // key is the lowercase short name of entity
            'association' => $association,
            'association.id' => $associationId,
        ] = $workerUtil->getTestAndRequiredAvoidRetryingIfNull(
            entityId: $entityId,
            requirePropertyPaths: $require,
        );

        $this->assertSame($entityId, $entity->getId());
        $this->assertSame(
            TestFactory::find($entityId)->getAssociation()->getId(),
            $association->getId(),
        );
        $this->assertSame(
            TestFactory::find($entityId)->getAssociation()->getId(),
            $associationId,
        );
    }

    public function testFetchedEntityAndRequiredPropertyPathWithSamePropertyNameAsEntity()
    {
        /** @var TestWorkerUtil $workerUtil */
        $workerUtil = self::$workerUtil;

        $entityId = 1;
        $require = [
            'association.id',
            'test',
        ];

        [
            0 => $entity, // key is the lowercase short name of entity
            'test' => $test, // key is the lowercase short name of entity
            'association.id' => $associationId,
        ] = $workerUtil->getTestAndRequiredAvoidRetryingIfNull(
            entityId: $entityId,
            requirePropertyPaths: $require,
        );

        $this->assertSame($entityId, $entity->getId());
        $this->assertSame(
            TestFactory::find($entityId)->getAssociation()->getId(),
            $associationId,
        );
        $this->assertSame(
            'TEST',
            $test,
        );
    }
}
