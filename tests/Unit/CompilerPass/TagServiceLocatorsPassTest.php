<?php

namespace GrinWay\Service\Tests\Unit;

use ArrayObject;
use GrinWay\Service\Pass\TagServiceLocatorsPass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Compiler\AutowirePass;
use Symfony\Component\DependencyInjection\Compiler\AutowireRequiredMethodsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Contracts\Service\Attribute\Required;

#[CoversClass(TagServiceLocatorsPassTest::class)]
class TagServiceLocatorsPassTest extends TestCase
{
    public const OWNER_TAG = 'owner_tag';

    public function testCollectedServiceLocatorsWithAllCorrectStaticMethodReturnType()
    {
        $container = new ContainerBuilder();
        $this->fillInContainer($container);

        // For working #[Required] attribute for autowired services
        $this->setBuiltInAutowireRequiredMethodsPass($container);

        // For working #[Autowire] attribute
        $this->setBuiltInAutowirePass($container);

        // COMPILER PASS IN ACTION
        $this->setThisTagServiceLocatorsPass($container);

        $resultCollection = $container->get('collector')->getCollection();

        $firstOwnerServices = $resultCollection[$this->getTagFormat('first_uniq_data')] ?? new ArrayObject();
        $this->assertCount(
            3,
            $firstOwnerServices,
        );
        $firstOwnerServices = \array_values(\iterator_to_array($firstOwnerServices));
        $this->assertInstanceOf(FirstOwnerSubService1::class, $firstOwnerServices[0]);
        $this->assertInstanceOf(FirstOwnerSubService2::class, $firstOwnerServices[1]);
        $this->assertInstanceOf(FirstOwnerSubService3::class, $firstOwnerServices[2]);

        $secondOwnerServices = $resultCollection[$this->getTagFormat('second_uniq_data')] ?? new ArrayObject();
        $this->assertCount(
            2,
            $secondOwnerServices,
        );
        $secondOwnerServices = \array_values(\iterator_to_array($secondOwnerServices));
        $this->assertInstanceOf(SecondOwnerSubService1::class, $secondOwnerServices[0]);
        $this->assertInstanceOf(SecondOwnerSubService2::class, $secondOwnerServices[1]);

        $thirdOwnerServices = $resultCollection[$this->getTagFormat('third_uniq_data')] ?? new ArrayObject();
        $this->assertCount(
            1,
            $thirdOwnerServices,
        );
        $thirdOwnerServices = \array_values(\iterator_to_array($thirdOwnerServices));
        $thirdOwnerService = $thirdOwnerServices[0];
        $this->assertInstanceOf(ThirdOwnerSubService1::class, $thirdOwnerService);
        $this->assertInstanceOf(SomeService::class, $thirdOwnerService->getSomeService());
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
        $this->setThisTagServiceLocatorsPass($container);
    }

    /**
     * Helper
     */
    private function fillInContainer(ContainerBuilder $container): void
    {
        $container
            ->register(CollectorService::class)
            ->setPublic(true)
            ->setClass(CollectorService::class)//
        ;
        $container
            ->addAliases([
                'collector' => CollectorService::class,
            ])//
        ;

        $container
            ->register(FirstOwner::class)
            ->setPublic(true)
            ->setClass(FirstOwner::class)
            ->addTag(self::OWNER_TAG)//
        ;
        $container
            ->register(SecondOwner::class)
            ->setPublic(true)
            ->setClass(SecondOwner::class)
            ->addTag(self::OWNER_TAG)//
        ;
        $container
            ->register(ThirdOwner::class)
            ->setPublic(true)
            ->setClass(ThirdOwner::class)
            ->addTag(self::OWNER_TAG)//
        ;
        $container
            ->addAliases([
                'first_owner'  => FirstOwner::class,
                'second_owner' => SecondOwner::class,
                'third_owner'  => ThirdOwner::class,
            ])//
        ;

        $container
            ->register(FirstOwnerSubService1::class)
            ->setPublic(true)
            ->setClass(FirstOwnerSubService1::class)
            ->addTag($this->getTagFormat('first_uniq_data'))//
        ;
        $container
            ->register(FirstOwnerSubService2::class)
            ->setPublic(true)
            ->setClass(FirstOwnerSubService2::class)
            ->addTag($this->getTagFormat('first_uniq_data'))//
        ;
        $container
            ->register(FirstOwnerSubService3::class)
            ->setPublic(true)
            ->setClass(FirstOwnerSubService3::class)
            ->addTag($this->getTagFormat('first_uniq_data'))//
        ;
        $container
            ->addAliases([
                'first_sub_service_1' => FirstOwnerSubService1::class,
                'first_sub_service_2' => FirstOwnerSubService2::class,
                'first_sub_service_3' => FirstOwnerSubService3::class,
            ])//
        ;

        $container
            ->register(SecondOwnerSubService1::class)
            ->setPublic(true)
            ->setClass(SecondOwnerSubService1::class)
            ->addTag($this->getTagFormat('second_uniq_data'))//
        ;
        $container
            ->register(SecondOwnerSubService2::class)
            ->setPublic(true)
            ->setClass(SecondOwnerSubService2::class)
            ->addTag($this->getTagFormat('second_uniq_data'))//
        ;
        $container
            ->addAliases([
                'second_sub_service_1' => SecondOwnerSubService1::class,
                'second_sub_service_2' => SecondOwnerSubService2::class,
            ])//
        ;

        $container
            ->register(ThirdOwnerSubService1::class)
            ->setPublic(true)
            ->setAutowired(true)
            ->setClass(ThirdOwnerSubService1::class)
            ->addTag($this->getTagFormat('third_uniq_data'))//
        ;
        $container
            ->addAliases([
                'third_sub_service_1' => ThirdOwnerSubService1::class,
            ])//
        ;

        $container
            ->register(SomeService::class)
            ->setPublic(true)
            ->setClass(SomeService::class)//
        ;
        $container
            ->addAliases([
                'some_service' => SomeService::class,
            ])//
        ;
    }

    /**
     * Compiler pass in action
     */
    protected function setThisTagServiceLocatorsPass(ContainerBuilder $container): void
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
     * Check built in compiler pass
     */
    protected function setBuiltInAutowirePass(ContainerBuilder $container): void
    {
        (new AutowirePass())->process($container);
    }

    /**
     * Check built in compiler pass
     */
    protected function setBuiltInAutowireRequiredMethodsPass(ContainerBuilder $container): void
    {
        (new AutowireRequiredMethodsPass())->process($container);
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
    private SomeService $someService;

    #[Required]
    public function setSomeService(
        #[Autowire(service: SomeService::class)]
        SomeService $someService,
    ): self {
        $this->someService = $someService;
        return $this;
    }

    public function getSomeService(): SomeService
    {
        return $this->someService;
    }
}

/**
 * @internal for tests
 */
class SomeService
{
}