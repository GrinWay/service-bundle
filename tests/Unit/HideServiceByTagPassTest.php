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
    public const HIDE_BY_TAG_3 = 'hide_by_tag_3';

    public function testServicesWereHiddenByTagNameWithEmptyPrefix()
    {
        $container = new ContainerBuilder();
        $this->fillInTheContainer($container);

        // COMPILER PASS IN ACTION
        $this->process(
            $container,
            '',
        );

        // First service was hidden (by first tag comparison)
        $this->assertFalse($container->hasDefinition('one'));
        $this->assertTrue($container->hasDefinition(
            \sprintf(
                '.%s.one_class_name',
                self::HIDE_BY_TAG_1,
            )
        ));
        $this->assertFalse($container->hasDefinition(
            \sprintf(
                '.%s.one_class_name',
                self::HIDE_BY_TAG_2,
            )
        ));
        $this->assertFalse($container->hasDefinition(
            \sprintf(
                '.%s.one_class_name',
                self::HIDE_BY_TAG_3,
            )
        ));
        // Second service was hidden (by first tag comparison)
        $this->assertFalse($container->hasDefinition('two'));
        $this->assertTrue($container->hasDefinition(
            \sprintf(
                '.%s.two_class_name',
                self::HIDE_BY_TAG_1,
            )
        ));
        $this->assertFalse($container->hasDefinition(
            \sprintf(
                '.%s.two_class_name',
                self::HIDE_BY_TAG_2,
            )
        ));
        // Third service was hidden (by first tag comparison)
        $this->assertFalse($container->hasDefinition('three'));
        $this->assertTrue($container->hasDefinition(
            \sprintf(
                '.%s.three_class_name',
                self::HIDE_BY_TAG_3,
            )
        ));

        $this->assertTrue($container->hasDefinition('four'));
    }

    public function testServicesWereHiddenByTagNameWithNotEmptyPrefix()
    {
        $container = new ContainerBuilder();
        $this->fillInTheContainer($container);

        // COMPILER PASS IN ACTION
        $this->process(
            $container,
            'notEmptyPrefix',
        );

        // First service was hidden (by first tag comparison)
        $this->assertFalse($container->hasDefinition('one'));
        $this->assertTrue($container->hasDefinition(
            \sprintf(
                '.not_empty_prefix.%s.one_class_name',
                self::HIDE_BY_TAG_1,
            )
        ));
        $this->assertFalse($container->hasDefinition(
            \sprintf(
                '.not_empty_prefix.%s.one_class_name',
                self::HIDE_BY_TAG_2,
            )
        ));
        $this->assertFalse($container->hasDefinition(
            \sprintf(
                '.not_empty_prefix.%s.one_class_name',
                self::HIDE_BY_TAG_3,
            )
        ));
        // Second service was hidden (by first tag comparison)
        $this->assertFalse($container->hasDefinition('two'));
        $this->assertTrue($container->hasDefinition(
            \sprintf(
                '.not_empty_prefix.%s.two_class_name',
                self::HIDE_BY_TAG_1,
            )
        ));
        $this->assertFalse($container->hasDefinition(
            \sprintf(
                '.not_empty_prefix.%s.two_class_name',
                self::HIDE_BY_TAG_2,
            )
        ));
        // Third service was hidden (by first tag comparison)
        $this->assertFalse($container->hasDefinition('three'));
        $this->assertTrue($container->hasDefinition(
            \sprintf(
                '.not_empty_prefix.%s.three_class_name',
                self::HIDE_BY_TAG_3,
            )
        ));

        $this->assertTrue($container->hasDefinition('four'));
    }

    public function testServicesWereHiddenByTagNameWithDotPrefix()
    {
        $container = new ContainerBuilder();
        $this->fillInTheContainer($container);

        // COMPILER PASS IN ACTION
        $this->process(
            $container,
            '.',
        );

        // First service was hidden (by first tag comparison)
        $this->assertFalse($container->hasDefinition('one'));
        $this->assertTrue($container->hasDefinition(
            \sprintf(
                '.%s.one_class_name',
                self::HIDE_BY_TAG_1,
            )
        ));
        $this->assertFalse($container->hasDefinition(
            \sprintf(
                '.%s.one_class_name',
                self::HIDE_BY_TAG_2,
            )
        ));
        $this->assertFalse($container->hasDefinition(
            \sprintf(
                '.%s.one_class_name',
                self::HIDE_BY_TAG_3,
            )
        ));
        // Second service was hidden (by first tag comparison)
        $this->assertFalse($container->hasDefinition('two'));
        $this->assertTrue($container->hasDefinition(
            \sprintf(
                '.%s.two_class_name',
                self::HIDE_BY_TAG_1,
            )
        ));
        $this->assertFalse($container->hasDefinition(
            \sprintf(
                '.%s.two_class_name',
                self::HIDE_BY_TAG_2,
            )
        ));
        // Third service was hidden (by first tag comparison)
        $this->assertFalse($container->hasDefinition('three'));
        $this->assertTrue($container->hasDefinition(
            \sprintf(
                '.%s.three_class_name',
                self::HIDE_BY_TAG_3,
            )
        ));

        $this->assertTrue($container->hasDefinition('four'));
    }

    /**
     * Helper
     */
    private function fillInTheContainer(ContainerBuilder $container): void
    {
        $container
            ->register('one')
            ->setClass(OneClassName::class)
            ->addTag(self::HIDE_BY_TAG_1)
            ->addTag(self::HIDE_BY_TAG_2)
            ->addTag(self::HIDE_BY_TAG_3)//
        ;
        $container
            ->register('two')
            ->setClass(TwoClassName::class)
            ->addTag(self::HIDE_BY_TAG_2)
            ->addTag(self::HIDE_BY_TAG_1)//
        ;
        $container
            ->register('three')
            ->setClass(ThreeClassName::class)
            ->addTag(self::HIDE_BY_TAG_3)//
        ;
        $container
            ->register('four')
            ->setPublic(true)
            ->setArguments([new Reference('TEST_SERVICE_ID')])//
        ;
    }

    /**
     * Compiler pass in action
     */
    protected function process(ContainerBuilder $container, string $prefix): void
    {
        (new HideServiceByTagPass(
            $prefix,
            self::HIDE_BY_TAG_1,
            self::HIDE_BY_TAG_2,
            self::HIDE_BY_TAG_3,
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

/**
 * @internal for test service definition
 */
class ThreeClassName
{
}
