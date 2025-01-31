<?php

namespace GrinWay\Service\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
            foreach ($container->findTaggedServiceIds($tag) as $serviceId => $serviceTagAttributes) {
                if ($container->hasDefinition($serviceId)) {
                    $definition = $container->findDefinition($serviceId);
                    $class = $definition->getClass();
                    $definition->setClass($class);
                    $shortClassName = (new \ReflectionClass($class))->getShortName();
                    $shortClassName = (string)u($shortClassName)->snake();
                    $newId = \sprintf(
                        '%s%s.%s',
                        $this->hiddenServiceIdPrefix,
                        $tag,
                        $shortClassName,
                    );
                    $container->removeDefinition($serviceId);
                    $container->setDefinition($newId, $definition);
                }
            }
        }
    }
}
