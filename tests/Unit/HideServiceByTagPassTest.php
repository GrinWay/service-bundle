<?php

namespace GrinWay\Service\Tests\Unit;

use GrinWay\Service\Pass\HideServiceByTagPass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

#[CoversClass(HideServiceByTagPass::class)]
class HideServiceByTagPassTest extends TestCase
{
    public const HIDE_BY_TAG_1 = 'hide_by_tag_1';
    public const HIDE_BY_TAG_2 = 'hide_by_tag_2';

    public function testServicesWereHiddenByTagNameWithEmptyPrefix()
    {
        $container = new ContainerBuilder();
        $container
            ->register('one')
            ->setClass(OneClassName::class)
            ->addTag(self::HIDE_BY_TAG_1)
            ->addTag(self::HIDE_BY_TAG_2)//
        ;
        $container
            ->register('two')
            ->setClass(TwoClassName::class)
            ->addTag(self::HIDE_BY_TAG_2)//
        ;

        $container
            ->register('three')
            ->setClass(self::class);
        $container
            ->register('four')
            ->setClass(self::class)
            ->setPublic(true)
            ->setArguments([new Reference('TEST_SERVICE_ID')]);

        // COMPILER PASS IN ACTION
        $this->process(
            $container,
            '',
        );

        $this->assertFalse($container->hasDefinition('one'));
        $this->assertFalse($container->hasDefinition('two'));
//        $this->assertTrue($container->hasDefinition(
//            \sprintf(
//                '.%s.one_class_name',
//                self::HIDE_BY_TAG_1,
//            )
//        ));
        $this->assertTrue($container->hasDefinition(
            \sprintf(
                '.%s.one_class_name',
                self::HIDE_BY_TAG_2,
            )
        ));
        $this->assertTrue($container->hasDefinition(
            \sprintf(
                '.%s.two_class_name',
                self::HIDE_BY_TAG_2,
            )
        ));

        $this->assertTrue($container->hasDefinition('three'));
        $this->assertTrue($container->hasDefinition('four'));
    }

//    public function testServicesWereHiddenByTagNameWithNotEmptyPrefix()
//    {
//    }

//    public function testServicesWereHiddenByTagNameWithDotPrefix()
//    {
//    }

    protected function process(ContainerBuilder $container, string $prefix): void
    {
        (new HideServiceByTagPass(
            $prefix,
            self::HIDE_BY_TAG_1,
            self::HIDE_BY_TAG_2,
        ))->process($container);
    }
}

/**
 * @internal for test service definition
 */
class OneClassName
{
}

/**
 * @internal for test service definition
 */
class TwoClassName
{
}
