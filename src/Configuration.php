<?php

namespace GrinWay\Service;

use function Symfony\Component\String\u;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use GrinWay\Service\{
    GrinWayServiceExtension
};
use GrinWay\Service\Service\{
    ConfigService
};

class Configuration implements ConfigurationInterface
{
    public function __construct(
        private $locale,
        private $timezone,
        private $appEnv,
        private $localDriveForTest,
        private $loadPacksConfigs,
        private $grinWayServiceYearRegex,
        private $grinWayServiceYearRegexFull,
        private $grinWayServiceIpV4Regex,
        private $grinWayServiceSlashOfIpRegex,
        private $grinWayServiceStartOfWinSysFileRegex,
        private $globalInstanceofRelPath,
    ) {
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(GrinWayServiceExtension::PREFIX);

        $treeBuilder->getRootNode()
            ->info(''
                . 'You can copy this example: "'
                . \dirname(__DIR__)
                . DIRECTORY_SEPARATOR . 'config'
                . DIRECTORY_SEPARATOR . 'packages'
                . DIRECTORY_SEPARATOR . 'grin_way_service.yaml'
                . '"')
            ->children()

                ->scalarNode(GrinWayServiceExtension::LOCALE)
                    ->info('Locale for services')
                    //->isRequired()
                    ->defaultValue($this->locale)
                    //->defaultValue('%grin_way_generic_parts.locale%') Don't work, it's a simple string if defaultValue
                ->end()

                ->scalarNode(GrinWayServiceExtension::TIMEZONE)
                    ->info('Timezone for services')
                    //->isRequired()
                    ->defaultValue($this->timezone)
                ->end()

                ->scalarNode(GrinWayServiceExtension::APP_ENV)
                    //->isRequired()
                    ->defaultValue($this->appEnv)
                ->end()

                ->scalarNode(GrinWayServiceExtension::LOCAL_DRIVE_FOR_TEST)
                    //->isRequired()
                    ->defaultValue($this->localDriveForTest)
                ->end()

                ->arrayNode(ConfigService::CONFIG_SERVICE_KEY)
                    ->info('the packs whose config will be loaded when GrinWay\\Service\\Service\\ConfigService creates')
                    ->defaultValue($this->loadPacksConfigs)
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode(ConfigService::PACK_NAME)
                                ->info('it\'s a name of the pack with or without the .yaml extension')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode(ConfigService::PACK_REL_PATH)
                                ->info('it\'s a relative path of the pack file')
                                ->defaultValue(ConfigService::DEFAULT_PACK_REL_PATH)
                            ->end()
                            ->booleanNode(ConfigService::LAZY_LOAD)
                                ->info('Is it lazy loading')
                                ->defaultValue(ConfigService::DEFAULT_LAZY_LOAD)
                            ->end()
                            ->scalarNode(ConfigService::DOES_NOT_EXIST_MESS)
                                ->info('Message if the file does not exist')
                                ->defaultValue(ConfigService::DEFAULT_DOES_NOT_EXIST_MESS)
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->scalarNode(GrinWayServiceExtension::YEAR_REGEX_KEY)
                    ->info('The regular expression of the year\'s number')
                    ->defaultValue($this->grinWayServiceYearRegex)
                ->end()

                ->scalarNode(GrinWayServiceExtension::YEAR_REGEX_FULL_KEY)
                    ->info('The regular expression of the year\'s full number')
                    ->defaultValue($this->grinWayServiceYearRegexFull)
                ->end()

                ->scalarNode(GrinWayServiceExtension::IP_V_4_REGEX_KEY)
                    ->info('The regular expression of ip v4')
                    ->defaultValue($this->grinWayServiceIpV4Regex)
                ->end()

                ->scalarNode(GrinWayServiceExtension::SLASH_OF_IP_REGEX_KEY)
                    ->info('The regular expression of ip\'s slashes')
                    ->defaultValue($this->grinWayServiceSlashOfIpRegex)
                ->end()

                ->scalarNode(GrinWayServiceExtension::START_OF_WIN_SYS_FILE_REGEX)
                    ->info('The regular expression of windows hidden system files')
                    ->defaultValue($this->grinWayServiceStartOfWinSysFileRegex)
                ->end()

                ->scalarNode(GrinWayServiceExtension::GLOBAL_INSTANCEOF_REL_PATH)
                    ->info('The relative path to directory where there is the "' . GrinWayServiceExtension::GLOBAL_INSTANCEOF_FILENAME . '" file to assign tags globally')
                    ->defaultValue($this->globalInstanceofRelPath)
                ->end()

            ->end()
        ;

        //$treeBuilder->setPathSeparator('/');

        return $treeBuilder;
    }

    //###> HELPERS ###
}
