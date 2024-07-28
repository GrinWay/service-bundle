<?php

namespace GrinWay\Service;

use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\DependencyInjection\Definition;
use GrinWay\Service\Configuration;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use GrinWay\Service\Service\ServiceContainer;
use GrinWay\Service\Service\ConfigService;
use GrinWay\Service\Service\ArrayService;
use GrinWay\Service\Service\BoolService;
use GrinWay\Service\Service\DoctrineService;
use GrinWay\Service\Service\BufferService;
use GrinWay\Service\Service\CarbonService;
use GrinWay\Service\Service\ClipService;
use GrinWay\Service\Service\DumpInfoService;
use GrinWay\Service\Service\FilesystemService;
use GrinWay\Service\Service\HtmlService;
use GrinWay\Service\Service\ParserService;
use GrinWay\Service\Service\RandomPasswordService;
use GrinWay\Service\Service\RegexService;
use GrinWay\Service\Service\StringService;
use GrinWay\Service\Service\OSService;

class GrinWayServiceExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    public const PREFIX = 'grin_way_service';

    public const LOCALE = 'locale';
    public Parameter $localeParameter;
    public const TIMEZONE = 'timezone';
    public Parameter $timezoneParameter;

    public const APP_ENV = 'app_env';
    public const LOCAL_DRIVE_FOR_TEST = 'local_drive_for_test';
    public const FAKER_SERVICE_KEY = 'faker';
    public const CARBON_FACTORY_SERVICE_KEY = 'carbon_factory_immutable';

    public const YEAR_REGEX_KEY = 'year_regex';
    public const YEAR_REGEX_FULL_KEY = 'year_regex_full';
    public const IP_V_4_REGEX_KEY = 'ip_v4_regex';
    public const SLASH_OF_IP_REGEX_KEY = 'slash_of_ip_regex';
    public const START_OF_WIN_SYS_FILE_REGEX = 'start_of_win_sys_file_regex';
    public const GLOBAL_INSTANCEOF_REL_PATH = 'global_instanceof_rel_path';
    public const GLOBAL_INSTANCEOF_FILENAME = '_instanceof.yaml';

    public function __construct(
        //private readonly BoolService $boolService,
    ) {
    }

    public function getAlias(): string
    {
        return self::PREFIX;
    }

    public function prepend(ContainerBuilder $container)
    {
        ServiceContainer::loadYaml(
            $container,
            __DIR__ . '/..',
            [
                ['config', 'services.yaml'],
            ],
        );
    }

    public function getConfiguration(
        array $config,
        ContainerBuilder $container,
    ) {
        return new Configuration(
            locale:     $container->getParameter(
                ServiceContainer::getParameterName(self::PREFIX, self::LOCALE),
            ),
            timezone:   $container->getParameter(
                ServiceContainer::getParameterName(self::PREFIX, self::TIMEZONE),
            ),
            appEnv: $container->getParameter(
                ServiceContainer::getParameterName(self::PREFIX, self::APP_ENV),
            ),
            localDriveForTest: $container->getParameter(
                ServiceContainer::getParameterName(self::PREFIX, self::LOCAL_DRIVE_FOR_TEST),
            ),
            loadPacksConfigs: $container->getParameter(
                ServiceContainer::getParameterName(self::PREFIX, ConfigService::CONFIG_SERVICE_KEY),
            ),
            grinWayServiceYearRegex: $container->getParameter(
                ServiceContainer::getParameterName(self::PREFIX, self::YEAR_REGEX_KEY),
            ),
            grinWayServiceYearRegexFull: $container->getParameter(
                ServiceContainer::getParameterName(self::PREFIX, self::YEAR_REGEX_FULL_KEY),
            ),
            grinWayServiceIpV4Regex: $container->getParameter(
                ServiceContainer::getParameterName(self::PREFIX, self::IP_V_4_REGEX_KEY),
            ),
            grinWayServiceSlashOfIpRegex:    $container->getParameter(
                ServiceContainer::getParameterName(self::PREFIX, self::SLASH_OF_IP_REGEX_KEY),
            ),
            grinWayServiceStartOfWinSysFileRegex:    $container->getParameter(
                ServiceContainer::getParameterName(self::PREFIX, self::START_OF_WIN_SYS_FILE_REGEX),
            ),
            globalInstanceofRelPath:    $container->getParameter(
                ServiceContainer::getParameterName(self::PREFIX, self::GLOBAL_INSTANCEOF_REL_PATH),
            ),
        );
    }

    public function loadInternal(array $config, ContainerBuilder $container): void
    {
        $this->setContainerParameters(
            $config,
            $container,
        );
        $this->setContainerDefinitions(
            $config,
            $container,
        );
        $this->setContainerTags(
            $container,
        );
    }

    private function setContainerParameters(
        array $config,
        ContainerBuilder $container,
    ) {
        $pa = PropertyAccess::createPropertyAccessor();

        ServiceContainer::setParametersForce(
            $container,
            callbackGetValue: static function ($key) use (&$config, $pa) {
                return $pa->getValue($config, '[' . $key . ']');
            },
            parameterPrefix: self::PREFIX,
            keys: [
            self::LOCALE,
            self::TIMEZONE,
            self::APP_ENV,
            self::LOCAL_DRIVE_FOR_TEST,
            self::YEAR_REGEX_KEY,
            self::YEAR_REGEX_FULL_KEY,
            self::IP_V_4_REGEX_KEY,
            self::SLASH_OF_IP_REGEX_KEY,
            ],
        );

        ServiceContainer::setParametersForce(
            $container,
            callbackGetValue: static function ($key) use (&$config, $pa) {
                $loadPacksConfigs = [];
                $configsService = $pa->getValue($config, '[' . $key . ']');
                foreach ($configsService as $configService) {
                    //###>
                    $packName = null;
                    if (isset($configService[ConfigService::PACK_NAME])) {
                        $packName = $configService[ConfigService::PACK_NAME];
                    }
                    $packRelPath = null;
                    if (isset($configService[ConfigService::PACK_REL_PATH])) {
                        $packRelPath = $configService[ConfigService::PACK_REL_PATH];
                    }
                    if ($packName == false) {
                        continue;
                    }
                    if ($packRelPath == false) {
                        $packRelPath = null;
                    }

                    $lazyLoad = $configService[ConfigService::LAZY_LOAD]
                    ?? ConfigService::DEFAULT_LAZY_LOAD
                    ;

                    $doesNotExistMess = $configService[ConfigService::DOES_NOT_EXIST_MESS]
                    ?? ConfigService::DEFAULT_DOES_NOT_EXIST_MESS
                    ;

                    $loadPacksConfigs [] = [
                    ConfigService::PACK_NAME            => $packName,
                    ConfigService::PACK_REL_PATH        => $packRelPath,
                    ConfigService::LAZY_LOAD            => $lazyLoad,
                    ConfigService::DOES_NOT_EXIST_MESS  => $doesNotExistMess,
                    ];
                }
                return $loadPacksConfigs;
            },
            parameterPrefix: self::PREFIX,
            keys: [
                ConfigService::CONFIG_SERVICE_KEY,
            ],
        );

        //\dd($container->getParameter('grin_way_service.load_packs_configs'));
    }


    //###> HELPERS ###


    private function setRestContainerDefinitions(
        ContainerBuilder $container,
    ): void {
        foreach (
            [
            [
                ArrayService::class,
                ArrayService::class,
                false,
            ],
            [
                BoolService::class,
                BoolService::class,
                false,
            ],
            [
                BufferService::class,
                BufferService::class,
                false,
            ],
            [
                CarbonService::class,
                CarbonService::class,
                false,
            ],
            [
                ClipService::class,
                ClipService::class,
                false,
            ],
            [
                DumpInfoService::class,
                DumpInfoService::class,
                false,
            ],
            [
                FilesystemService::class,
                FilesystemService::class,
                false,
            ],
            [
                HtmlService::class,
                HtmlService::class,
                false,
            ],
            [
                ParserService::class,
                ParserService::class,
                false,
            ],
            [
                RandomPasswordService::class,
                RandomPasswordService::class,
                false,
            ],
            [
                RegexService::class,
                RegexService::class,
                false,
            ],
            [
                StringService::class,
                StringService::class,
                false,
            ],
            [
                OSService::class,
                OSService::class,
                false,
            ],
            [
                ConfigService::class,
                ConfigService::class,
                false,
            ],
            [
                DoctrineService::class,
                DoctrineService::class,
                false,
            ],
            ] as [ $id, $class, $isAbstract ]
        ) {
            $container
                ->setDefinition(
                    $id,
                    (new Definition($class))
                        ->setAutowired(true)
                        ->setAbstract($isAbstract),
                )
            ;
        }

        foreach (
            [
            [
                StringService::class,
                [
                    '$grinWayServiceYearRegex' => $container->getParameter(
                        ServiceContainer::getParameterName(
                            self::PREFIX,
                            self::YEAR_REGEX_KEY,
                        ),
                    ),
                    '$grinWayServiceYearRegexFull' => $container->getParameter(
                        ServiceContainer::getParameterName(
                            self::PREFIX,
                            self::YEAR_REGEX_FULL_KEY,
                        ),
                    ),
                    '$grinWayServiceIpV4Regex' => $container->getParameter(
                        ServiceContainer::getParameterName(
                            self::PREFIX,
                            self::IP_V_4_REGEX_KEY,
                        ),
                    ),
                    '$grinWayServiceSlashOfIpRegex' => $container->getParameter(
                        ServiceContainer::getParameterName(
                            self::PREFIX,
                            self::SLASH_OF_IP_REGEX_KEY,
                        ),
                    ),
                ],
            ],
            [
                ConfigService::class,
                [
                    '$grinWayServiceProjectDir' => $container->getParameter('kernel.project_dir'),
                    '$grinWayServicePackageFilenames' => $container->getParameter(
                        ServiceContainer::getParameterName(self::PREFIX, ConfigService::CONFIG_SERVICE_KEY),
                    ),
                ],
            ],
            [
                FilesystemService::class,
                [
                    '$grinWayServiceLocalDriveForTest' => $container->getParameter(
                        ServiceContainer::getParameterName(self::PREFIX, self::LOCAL_DRIVE_FOR_TEST),
                    ),
                    '$grinWayServiceAppEnv' => $container->getParameter(
                        ServiceContainer::getParameterName(self::PREFIX, self::APP_ENV),
                    ),
                    '$grinWayServiceCarbonFactoryImmutable' => $container->getDefinition(
                        ServiceContainer::getParameterName(self::PREFIX, self::CARBON_FACTORY_SERVICE_KEY),
                    ),
                ],
            ],
            [
                CarbonService::class,
                [
                    '$grinWayServiceCarbonFactoryImmutable' => $container->getDefinition(
                        ServiceContainer::getParameterName(self::PREFIX, self::CARBON_FACTORY_SERVICE_KEY),
                    ),
                ],
            ],
            ] as [ $id, $args ]
        ) {
            if ($container->hasDefinition($id)) {
                $container
                    ->getDefinition($id)
                    ->setArguments($args)
                ;
            }
        }

        //###>
        foreach (
            [
            [
                OSService::class,
            ],
            ] as [ $id ]
        ) {
            if ($container->hasDefinition($id)) {
                $container
                    ->getDefinition($id)
                    ->setShared(false)
                ;
            }
        }
    }

    private function carbonDefinition(
        array $config,
        ContainerBuilder $container,
    ): void {
        $carbon = new Definition(
            class: \Carbon\FactoryImmutable::class,
            arguments: [
                '$settings'         => [
                    'locale'                    => $this->localeParameter,
                    'strictMode'                => true,
                    'timezone'                  => $this->timezoneParameter,
                    'toStringFormat'            => 'd.m.Y H:i:s P',
                    'monthOverflow'             => true,
                    'yearOverflow'              => true,
                ],
            ],
        );
        $container->setDefinition(
            id: ServiceContainer::getParameterName(self::PREFIX, self::CARBON_FACTORY_SERVICE_KEY),
            definition: $carbon,
        );
    }

    private function fakerDefinition(
        array $config,
        ContainerBuilder $container,
    ): void {
        $faker = (new Definition(\Faker\Factory::class, []))
            ->setFactory([\Faker\Factory::class, 'create'])
            ->setArgument(0, $this->localeParameter)
        ;
        //\dd($config['locale']);
        $faker = $container->setDefinition(
            id: ServiceContainer::getParameterName(self::PREFIX, self::FAKER_SERVICE_KEY),
            definition: $faker,
        );
    }

    private function setContainerDefinitions(
        array $config,
        ContainerBuilder $container,
    ) {
        $this->setParameterObjects();
        $this->carbonDefinition(
            $config,
            $container,
        );
        $this->fakerDefinition(
            $config,
            $container,
        );
        $this->setRestContainerDefinitions(
            $container,
        );
        $this->setGlobalInstanceOfRegisterForAutoconfiguration(
            $config,
            $container,
        );
    }

    private function setContainerTags(ContainerBuilder $container)
    {
        /*
        $container
            ->registerForAutoconfiguration(\GrinWay\Service\<>Interface::class)
            ->addTag(GrinWayTag::<>)
        ;
        */
    }

    private function setParameterObjects(): void
    {
        /* to use in this object */

        $this->localeParameter = new Parameter(ServiceContainer::getParameterName(
            self::PREFIX,
            self::LOCALE,
        ));
        $this->timezoneParameter = new Parameter(ServiceContainer::getParameterName(
            self::PREFIX,
            self::TIMEZONE,
        ));
    }

    /**
    * FileLocator for "%kernel.project_dir%/ <relPath> /_instanceof.yaml":
    *
    * _instanceof.yaml has the same syntax as _instanceof of services.yaml
    * only works for all the project
    *
    * # if one element that's a TAG_NAME
    * INTERFACE1:
    *    tags:
    *    -  TAG_NAME1
    *    -  TAG_NAME2
    *
    * # TAG_NAMES named as "name"
    * INTERFACE2:
    *    tags:
    *    -  name: TAG_NAME1
    *    -  name: TAG_NAME2
    *
    * # TAG_NAMES named as "name"
    * INTERFACE3:
    *    tags:
    *    -  name: TAG_NAME1
    *       dop_attr: NAME
    *    -  name: TAG_NAME2
    *
    * # TAG_NAMES as keys
    * INTERFACE4:
    *    tags:
    *       TAG_NAME1:
    *           dop_attr: NAME
    *       TAG_NAME2:
    *           dop_attr: NAME
    */
    public function setGlobalInstanceOfRegisterForAutoconfiguration(
        array $config,
        ContainerBuilder $container,
    ): void {
        $pa = PropertyAccess::createPropertyAccessor();

        $relPath = $pa->getValue($config, '[' . self::GLOBAL_INSTANCEOF_REL_PATH . ']');
        if (null === $relPath) {
            return;
        }

        $kernelProjectDir = $container->getParameter('kernel.project_dir');
        $absPathToYamlInstanceOf = Path::makeAbsolute(
            path: $relPath,
            basePath: $kernelProjectDir,
        );

        $fileLocator = new FileLocator($absPathToYamlInstanceOf);
        $absPathToYamlInstanceOf = Path::normalize(
            $fileLocator->locate(self::GLOBAL_INSTANCEOF_FILENAME, first: true),
        );

        $interfaces = Yaml::parseFile(
            $absPathToYamlInstanceOf,
            Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE
            | Yaml::PARSE_OBJECT
            | Yaml::PARSE_DATETIME
            | Yaml::PARSE_CONSTANT
            | Yaml::PARSE_CUSTOM_TAGS
        );
        if (!\is_array($interfaces)) {
            return;
        }
        foreach ($interfaces as $interface => $instanceAndTheirTags) {
            $tagsAndAttributes = $pa->getValue($instanceAndTheirTags, '[tags]');

            if (null === $interface) {
                continue;
            }

            foreach ($tagsAndAttributes as $tagKey => $tagAttributes) {
                if (\is_int($tagKey)) {
                    if (\is_string($tagAttributes)) {
                        $tagName = $tagAttributes;
                        $tagAttributes = [];
                    } else {
                        $tagName = $pa->getValue($tagAttributes, '[name]');
                        unset($tagAttributes['name']);
                    }
                } else {
                    $tagName = $tagKey;
                }

                if (!\is_string($tagName)) {
                    $message = \sprintf(
                        'Name of the tag must me string and must be: "key" or [name] of assotiative element or one element in tag array',
                    );
                    throw new \Exception($message);
                }
                if (empty($tagAttributes)) {
                    $tagAttributes = [];
                }

                $container->registerForAutoconfiguration($interface)
                    ->addTag($tagName, $tagAttributes)
                    ->setAutoconfigured(true)
                ;
            }
        }
    }
}
