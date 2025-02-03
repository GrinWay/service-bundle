<?php

namespace GrinWay\Service\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use function Symfony\Component\String\u;

class HideServiceByTagPass implements CompilerPassInterface
{
    private readonly array $tags;
    private readonly string $hiddenServiceIdPrefix;

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
        }
        $this->tags = $tags;
    }

    public function process(ContainerBuilder $container)
    {
        foreach ($this->tags as $tag) {
            foreach ($container->findTaggedServiceIds($tag) as $currentId => $serviceTagAttributes) {
                if ($container->hasDefinition($currentId)) {
                    $definition = $container->findDefinition($currentId);
                    $newId = $this->getNewId($definition, $tag);
                    $container->removeDefinition($currentId);
                    $container->setDefinition($newId, $definition);
                }
            }
        }
    }

    /**
     * Helper
     */
    private function getShortClassName(Definition $definition): string
    {
        $class = $definition->getClass();
        $shortClassName = (new \ReflectionClass($class))->getShortName();
        return (string)u($shortClassName)->snake();
    }

    /**
     * Helper
     */
    private function getNewId(Definition $definition, string $tag): string
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
