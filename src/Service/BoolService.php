<?php

namespace GrinWay\Service\Service;

use function Symfony\Component\String\{
    u,
    b
};

use Symfony\Component\Finder\{
    SplFileInfo,
    Finder
};
use Symfony\Component\Filesystem\{
    Path,
    Filesystem
};
use Symfony\Component\OptionsResolver\{
    Options,
    OptionsResolver
};
use Symfony\Component\Yaml\{
    Tag\TaggedValue,
    Yaml
};
use Symfony\Component\HttpFoundation\{
    Request,
    RequestStack,
    Session\Session
};
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BoolService
{
    public function __construct()
    {
    }

    //###> API ###

    /*
        Returns null if the key doesn't exist
    */
    public function isGet(
        array $array,
        string $key,
    ): mixed {
        return isset($array[$key])
            ? $array[$key]
            : null
        ;
    }

    /**/
    public function isCurrentConsolePathStartsWithSlash(): bool
    {
        $cwd = \getcwd();

        return false
            || \str_starts_with($cwd, '/')
            || \str_starts_with($cwd, '\\')
        ;
    }

    //###< API ###


    //###> STATIC API ###

    /**
    *
    */
    public static function isPropExist(object $obj, int|string $prop, bool $throw): bool
    {
        if (!isset($obj->{$prop})) {
            $mess = \sprintf(
                'The property name: "%s" does not exist in class: "%s"',
                $prop,
                \get_debug_type($obj),
            );
            if (true === $throw) {
                throw new \Exception($mess);
            }
            return false;
        }
        return true;
    }

    //###< STATIC API ###
}
