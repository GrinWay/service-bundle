<?php

namespace GrinWay\Service\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use function Symfony\Component\String\u;

/**
 * Hide service by their tags
 *
 * For instance not to show them publicly or not to collect them by: !tagged_iterator, !tagged_locator
 *
 * First found service by tag will be hidden and won't be hidden twice
 * because this compiler pass don't touch already hidden services
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class HideServiceByTagPass implements CompilerPassInterface
{
    private readonly array $tags;
    private readonly string $hiddenServiceIdPrefix;

    /**
     * @param string ...$tags Is sensitive to order (Hides by first comparison)
     */
    public function __construct(
        string                  $hiddenServiceIdPrefix,
        string                  ...$tags,
    )
    {
        if (!\str_starts_with($hiddenServiceIdPrefix, '.')) {
            if (empty($hiddenServiceIdPrefix)) {
                $this->hiddenServiceIdPrefix = '.';
            } else {
                $this->hiddenServiceIdPrefix = '.' . u($hiddenServiceIdPrefix)->snake() . '.';
            }
        } else {
            $this->hiddenServiceIdPrefix = '.';
        }
        $this->tags = $tags;
    }

    public function process(ContainerBuilder $container)
    {
        foreach ($this->tags as $tag) {
            foreach ($container->findTaggedServiceIds($tag) as $currentServiceId => $serviceTagAttributes) {
                if (!\str_starts_with($currentServiceId, '.') && $container->hasDefinition($currentServiceId)) {
                    $definition = $container->findDefinition($currentServiceId);
                    $newServiceId = $this->getNewServiceId($definition, $tag);
                    $container->removeDefinition($currentServiceId);
                    $container->setDefinition($newServiceId, $definition);
                }
            }
        }
    }

    /**
     * Helper
     */
    private function getShortClassName(Definition $definition): string
    {
        $fullQualifiedClassName = $definition->getClass();
        $shortClassName = (new \ReflectionClass($fullQualifiedClassName))->getShortName();
        return (string)u($shortClassName)->snake();
    }

    /**
     * Helper
     */
    private function getNewServiceId(Definition $definition, string $tag): string
    {
        $shortClassName = $this->getShortClassName($definition);
        return \sprintf(
            '%s%s.%s',
            $this->hiddenServiceIdPrefix,
            $tag,
            $shortClassName,
        );
    }
}
