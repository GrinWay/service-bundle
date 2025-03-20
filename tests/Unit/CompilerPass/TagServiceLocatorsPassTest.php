<?php

namespace GrinWay\Service\Tests\Unit;

use GrinWay\Service\Pass\TagServiceLocatorsPass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ServiceLocator;

#[CoversClass(TagServiceLocatorsPassTest::class)]
class TagServiceLocatorsPassTest extends TestCase
{
    public const OWNER_TAG = 'owner_tag';

    public function testCollectedServiceLocatorsWithAllCorrectStaticMethodReturnType()
    {
        $container = new ContainerBuilder();
        $this->fillInContainer($container);

        // COMPILER PASS IN ACTION
        $this->process($container);

        $resultCollection = $container->get('collector')->getCollection();

        $this->assertCount(
            3,
            $resultCollection[$this->getTagFormat('first_uniq_data')],
        );
        $this->assertCount(
            2,
            $resultCollection[$this->getTagFormat('second_uniq_data')],
        );
        $this->assertCount(
            1,
            $resultCollection[$this->getTagFormat('third_uniq_data')],
        );
    }

    public function testCollectedServiceLocatorsThrowsWhenStaticMethodReturnNotString()
    {
        $container = new ContainerBuilder();
        $this->fillInContainer($container);
        $container
            ->register('incorrect_owner')
            ->setClass(IncorrectOwner::class)
            ->addTag(self::OWNER_TAG)//
        ;
        // COMPILER PASS IN ACTION
        $this->expectException(\BadMethodCallException::class);
        $this->process($container);
    }

    /**
     * Helper
     */
    private function fillInContainer(ContainerBuilder $container): void
    {
        $container
            ->register('collector')
            ->setClass(CollectorService::class)//
        ;

        $container
            ->register('first_owner')
            ->setClass(FirstOwner::class)
            ->addTag(self::OWNER_TAG)//
        ;
        $container
            ->register('second_owner')
            ->setClass(SecondOwner::class)
            ->addTag(self::OWNER_TAG)//
        ;
        $container
            ->register('third_owner')
            ->setClass(ThirdOwner::class)
            ->addTag(self::OWNER_TAG)//
        ;

        $container
            ->register('first_sub_service_1')
            ->setClass(FirstOwnerSubService1::class)
            ->addTag($this->getTagFormat('first_uniq_data'))//
        ;
        $container
            ->register('first_sub_service_2')
            ->setClass(FirstOwnerSubService2::class)
            ->addTag($this->getTagFormat('first_uniq_data'))//
        ;
        $container
            ->register('first_sub_service_3')
            ->setClass(FirstOwnerSubService3::class)
            ->addTag($this->getTagFormat('first_uniq_data'))//
        ;
        $container
            ->register('second_sub_service_1')
            ->setClass(SecondOwnerSubService1::class)
            ->addTag($this->getTagFormat('second_uniq_data'))//
        ;
        $container
            ->register('second_sub_service_2')
            ->setClass(SecondOwnerSubService2::class)
            ->addTag($this->getTagFormat('second_uniq_data'))//
        ;
        $container
            ->register('third_sub_service_1')
            ->setClass(ThirdOwnerSubService1::class)
            ->addTag($this->getTagFormat('third_uniq_data'))//
        ;
    }

    /**
     * Compiler pass in action
     */
    protected function process(ContainerBuilder $container): void
    {
        (new TagServiceLocatorsPass(
            'collector',
            'collect',
            self::OWNER_TAG,
            'getUniqOwnerString',
            $this->getTagFormat('%s'),
        ))->process($container);
    }

    /**
     * Helper
     */
    private function getTagFormat(string $formatPart): string
    {
        return \sprintf('%s.sub_service', $formatPart);
    }
}

/**
 * @internal for tests (as a collector)
 */
class CollectorService
{
    private array $collection = [];

    public function collect(string $key, ServiceLocator $serviceLocator)
    {
        $this->collection[$key] = $serviceLocator;
    }

    public function getCollection(): array
    {
        return $this->collection;
    }
}

/**
 * @internal for tests (as an owner)
 */
class FirstOwner
{
    public static function getUniqOwnerString(): string
    {
        return 'first_uniq_data';
    }
}

/**
 * @internal for tests (as an owner)
 */
class SecondOwner
{
    public static function getUniqOwnerString(): string
    {
        return 'second_uniq_data';
    }
}

/**
 * @internal for tests (as an owner)
 */
class ThirdOwner
{
    public static function getUniqOwnerString(): string
    {
        return 'third_uniq_data';
    }
}

/**
 * @internal for tests (as an incorrect owner)
 */
class IncorrectOwner
{
    public static function getUniqOwnerString()
    {
        return 1;
    }
}

/**
 * @internal for tests (as a sub-service)
 *
 * Exactly "first" owner sub-service because its "first_uniq_data.sub_service" tag name
 */
class FirstOwnerSubService1
{
}

/**
 * @internal for tests (as a sub-service)
 *
 * Exactly "first" owner sub-service because its "first_uniq_data.sub_service" tag name
 */
class FirstOwnerSubService2
{
}


/**
 * @internal for tests (as a sub-service)
 *
 * Exactly "first" owner sub-service because its "first_uniq_data.sub_service" tag name
 */
class FirstOwnerSubService3
{
}


/**
 * @internal for tests (as a sub-service)
 *
 * Exactly "second" owner sub-service because its "second_uniq_data.sub_service" tag name
 */
class SecondOwnerSubService1
{
}

/**
 * @internal for tests (as a sub-service)
 *
 * Exactly "second" owner sub-service because its"second_uniq_data.sub_service" tag name
 */
class SecondOwnerSubService2
{
}


/**
 * @internal for tests (as a sub-service)
 *
 * Exactly "third" owner sub-service because its"third_uniq_data.sub_service" tag name
 */
class ThirdOwnerSubService1
{
}
