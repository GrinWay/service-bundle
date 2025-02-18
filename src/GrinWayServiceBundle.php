<?php

namespace GrinWay\Service;

use GrinWay\Service\Contract\Doctrine\DoctrineEventListenerInterface;
use GrinWay\Service\Validator\LikeNumeric;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

/**
 * @final
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class GrinWayServiceBundle extends AbstractBundle
{
    public const EXTENSION_ALIAS = 'grinway_service';
    public const BUNDLE_PREFIX = self::EXTENSION_ALIAS . '.';
    public const COMMAND_PREFIX = self::EXTENSION_ALIAS . ':';

    // https://symfony.com/doc/current/components/cache.html#stampede-prevention
    public const GENERIC_CACHE_TAG = self::EXTENSION_ALIAS;

    //###> DEFAULTS ###
    public const DEFAULT_CURRENCY_CACHE_LIFETIME = 86400;
    public const DEFAULT_DATA_TIME_FORMAT = 'd.m.Y H:i:s P';
    public const DEFAULT_TIMEZONE = '+00:00';
    public const DEFAULT_LOCALE = 'en';
    public const DEFAULT_CARBON_YEAR_OVERFLOW = true;
    public const DEFAULT_CARBON_MONTH_OVERFLOW = true;
    public const DEFAULT_CARBON_STRICT_MODE = true;
    //###< DEFAULTS ###

    protected string $extensionAlias = self::EXTENSION_ALIAS;

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()//

            ->scalarNode('locale')
            ->defaultValue(self::DEFAULT_LOCALE)
            ->end()//

            ->scalarNode('timezone')
            ->defaultValue(self::DEFAULT_TIMEZONE)
            ->end()//

            ->scalarNode('date_time_format')
            ->defaultValue(self::DEFAULT_DATA_TIME_FORMAT)
            ->end()//

            ->arrayNode('database')//
            ->children()
            //###> database array node ###

            ->scalarNode('ip')
            ->info('when prod: it\'s the database ip, when dev: docker container DSN when it\'s named network')
            ->isRequired()
            ->end()//

            ->scalarNode('database_name')
            ->isRequired()
            ->end()//

            ->scalarNode('port')
            ->isRequired()
            ->end()//

            ->scalarNode('user')
            ->isRequired()
            ->end()//

            ->scalarNode('backup_abs_dir')
            ->isRequired()
            ->end()//

            ->scalarNode('password')
            ->info('REQUIRED ONLY WHEN TEST')
            ->end()//

            //###< database array node ###
            ->end()
            ->end()//

            ->arrayNode('doctrine')//
            ->addDefaultsIfNotSet()
            ->children()
            //###> doctrine array node ###

            ->arrayNode('event_listeners')//
            ->canBeDisabled()
            ->children()
            //###> event_listeners array node ###

            ->arrayNode('auto_set_utc_date_time_before_to_database')//
            ->canBeDisabled()
            ->children()
            //###> auto_set_created_at_when_pre_persist array node ###

            //###< auto_set_created_at_when_pre_persist array node ###
            ->end()
            ->end()//

            ->arrayNode('auto_set_created_at_when_pre_persist')//
            ->canBeDisabled()
            ->children()
            //###> auto_set_created_at_when_pre_persist array node ###

            //###< auto_set_created_at_when_pre_persist array node ###
            ->end()
            ->end()//

            ->arrayNode('auto_set_updated_at_when_pre_update')//
            ->canBeDisabled()
            ->children()
            //###> auto_set_updated_at_when_pre_update array node ###

            //###< auto_set_updated_at_when_pre_update array node ###
            ->end()
            ->end()//

            //###< event_listeners array node ###
            ->end()
            ->end()//

            //###< doctrine array node ###
            ->end()
            ->end()//

            ->arrayNode('carbon')//
            ->children()
            //###> carbon array node ###

            ->booleanNode('strict_mode')
            ->defaultValue(self::DEFAULT_CARBON_STRICT_MODE)
            ->end()//

            ->booleanNode('month_overflow')
            ->defaultValue(self::DEFAULT_CARBON_MONTH_OVERFLOW)
            ->end()//

            ->booleanNode('year_overflow')
            ->defaultValue(self::DEFAULT_CARBON_YEAR_OVERFLOW)
            ->end()//

            //###< carbon array node ###
            ->end()
            ->end()//

            ->arrayNode('currency')
            ->children()//
            //###> currency array node ###

            ->scalarNode('fixer_api_key')//
            ->isRequired()
            ->cannotBeEmpty()
            ->end()//

            ->arrayNode('cache')//
            ->children()
            //###> cache array node ###

            ->scalarNode('lifetime')
            ->validate()->always()->then(static fn($v) => Validation::createCallable(new NotBlank(), new LikeNumeric())($v))->end() // only when explicitly configured
            ->defaultValue(self::DEFAULT_CURRENCY_CACHE_LIFETIME) // docs default
            ->end()//

            //###> cache array node ###
            ->end()
            ->end()//

            //###< currency array node ###
            ->end()
            ->end()//

            ->end()
            ->end()//
        ;
    }

    /**
     * Helper
     */
    private function setServiceContainerParameters(array $config, ContainerConfigurator $container): void
    {
        $env = $container->env();
        $parameters = $container->parameters();

        $parameters
            ->set(self::bundlePrefixed('database.database_name'), $config['database']['database_name'])//
            ->set(self::bundlePrefixed('database.ip'), $config['database']['ip'])//
            ->set(self::bundlePrefixed('database.port'), $config['database']['port'])//
            ->set(self::bundlePrefixed('database.user'), $config['database']['user'])//
            ->set(self::bundlePrefixed('database.backup_abs_dir'), $config['database']['backup_abs_dir'])//

            ->set(self::bundlePrefixed('locale'), $config['locale'] ?? self::DEFAULT_LOCALE)//
            ->set(self::bundlePrefixed('timezone'), $config['timezone'] ?? self::DEFAULT_TIMEZONE)//
            ->set(self::bundlePrefixed('date_time_format'), $config['date_time_format'] ?? self::DEFAULT_DATA_TIME_FORMAT)//

            ->set(self::bundlePrefixed('carbon.strict_mode'), $config['carbon']['strict_mode'] ?? self::DEFAULT_CARBON_STRICT_MODE)//
            ->set(self::bundlePrefixed('carbon.month_overflow'), $config['carbon']['month_overflow'] ?? self::DEFAULT_CARBON_MONTH_OVERFLOW)//
            ->set(self::bundlePrefixed('carbon.year_overflow'), $config['carbon']['year_overflow'] ?? self::DEFAULT_CARBON_YEAR_OVERFLOW)//

            ->set(self::bundlePrefixed('currency.fixer_api_key'), $config['currency']['fixer_api_key'])//
            ->set(self::bundlePrefixed('currency.cache.lifetime'), $config['currency']['cache']['lifetime'] ?? self::DEFAULT_CURRENCY_CACHE_LIFETIME)//

            ->set('grinway_service.logical_path.non_removable_cache.currency.fixer_api', '@GrinWayServiceBundle/non_removable_cache/currency/fixer_api.json')//
        ;

        if ('test' === $env) {
            $parameters
                ->set(self::bundlePrefixed('test.database.password'), $config['database']['password'])//
            ;
        }
    }

    /**
     * use loadExtension method instead
     */
//    public function getContainerExtension(): ?ExtensionInterface
//    {
//        return new GrinWay_Extension();
//    }

    /**
     * Before service container compiled
     */
    public function build(ContainerBuilder $builder): void
    {
        parent::build($builder);
        $this->registerCompilerPasses($builder);
    }

    /**
     * Before service container compiled (late for registering compiler pass here)
     */
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $this->importOtherBundleConfigurations($container, $builder);
        $this->registerForAutoconfiguration($container, $builder);
    }

    /**
     * After service container compiled
     *
     * Too late for registering compiler pass here
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $this->setServiceContainerParameters($config, $container);
        $this->setServiceContainerServices($config, $container);
        $this->removeServiceContainerServices($config, $container, $builder);
    }

    /**
     * Root directory of this bundle
     */
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    /**
     * Helper
     */
    public static function bundlePrefixed(string $name): string
    {
        return \sprintf(
            '%s%s',
            self::BUNDLE_PREFIX,
            \ltrim($name, '._:'),
        );
    }

    /**
     * Helper
     */
    public function absPath(string $path): string
    {
        return \sprintf(
            '%s/%s',
            \rtrim($this->getPath(), '/\\'),
            \ltrim($path, '/\\'),
        );
    }

    /**
     * Helper
     */
    private function registerCompilerPasses(ContainerBuilder $builder): void
    {
        $this->hideServices($builder);
    }

    /**
     * Helper
     */
    private function setServiceContainerServices(array $config, ContainerConfigurator $container): void
    {
        $container->import($this->absPath('config/services.yaml'));
    }

    /**
     * Helper
     */
    private function isAssetMapperAvailable(ContainerBuilder $builder): bool
    {
        if (!\interface_exists(AssetMapperInterface::class)) {
            return false;
        }

        // check that FrameworkBundle 6.3 or higher is installed
        $bundlesMetadata = $builder->getParameter('kernel.bundles_metadata');
        if (!isset($bundlesMetadata['FrameworkBundle'])) {
            return false;
        }

        return \is_file($bundlesMetadata['FrameworkBundle']['path'] . '/Resources/config/asset_mapper.php');
    }

    /**
     * Helper
     */
    private function assetMapperEnable(ContainerBuilder $builder): void
    {
        if (!$this->isAssetMapperAvailable($builder)) {
            return;
        }

        $builder->prependExtensionConfig('framework', [
            'asset_mapper' => [
                'paths' => [
                    __DIR__ . '/../assets/dist' => '@grinway/service-bundle',
                ],
            ],
        ]);
    }

    /**
     * Helper
     *
     * Self bundle's configuration is imported automatically
     */
    private function importOtherBundleConfigurations(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $this->assetMapperEnable($builder);
        $container->import($this->absPath('config/packages/framework_assets.yaml'));
        $container->import($this->absPath('config/packages/framework_http_client.yaml'));
        $container->import($this->absPath('config/packages/framework_messenger.yaml'));
        $container->import($this->absPath('config/packages/framework_notifier.yaml'));
        $container->import($this->absPath('config/packages/framework_property_access.yaml'));
        $container->import($this->absPath('config/packages/framework_request.yaml'));
        $container->import($this->absPath('config/packages/framework_serializer.yaml'));
        $container->import($this->absPath('config/packages/framework_translator.yaml'));
        $container->import($this->absPath('config/packages/framework_validation.yaml'));
        $container->import($this->absPath('config/packages/framework_test.yaml'));
        $container->import($this->absPath('config/packages/framework_cache.yaml'));
        $container->import($this->absPath('config/packages/doctrine.yaml'));
        /*
         * If you do, you influence main project!
         */
//        $container->import($this->absPath('config/packages/maker.yaml'));
    }

    /**
     * Helper
     */
    private function hideServices(ContainerBuilder $builder): void
    {
    }

    /**
     * Helper
     */
    private function registerForAutoconfiguration(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
//        foreach ([
//                     [
//                         'interface' => '',
//                         'tag_name' => '',
//                         'tag_attributes' => [
//                             'priority' => 0,
//                         ]
//                     ],
//                 ] as ['interface' => $interface, 'tag_name' => $tagName, 'tag_attributes' => $tagAttributes])
//            $builder->registerForAutoconfiguration($interface)
//                ->addTag($tagName, $tagAttributes)//
//            ;
    }

    private function removeDoctrineEventListeners(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $eventListenersAreDisabled = true !== $config['doctrine']['event_listeners']['enabled'];

        if ($eventListenersAreDisabled) {
            foreach ($builder->findTaggedServiceIds(DoctrineEventListenerInterface::TAG) as $serviceId => $tagAttributes) {
                $builder->removeDefinition($serviceId);
            }
            return;
        }

        $setUtcEventListenerIsDisabled = true !== $config['doctrine']['event_listeners']['auto_set_utc_date_time_before_to_database']['enabled'];
        $createdAtEventListenerIsDisabled = true !== $config['doctrine']['event_listeners']['auto_set_created_at_when_pre_persist']['enabled'];
        $updatedAtEventListenerIsDisabled = true !== $config['doctrine']['event_listeners']['auto_set_updated_at_when_pre_update']['enabled'];

        if ($setUtcEventListenerIsDisabled) {
            $builder->removeDefinition('.grinway_service.event_listener.doctrine.date_time_to_utc_before_to_database');
        }
        if ($createdAtEventListenerIsDisabled) {
            $builder->removeDefinition('.grinway_service.event_listener.doctrine.created_at');
        }
        if ($updatedAtEventListenerIsDisabled) {
            $builder->removeDefinition('.grinway_service.event_listener.doctrine.updated_at');
        }
    }

    /**
     * Helper
     *
     * @internal
     */
    protected function removeServiceContainerServices(array $config, ContainerConfigurator $container, ContainerBuilder $builder)
    {
        $this->removeDoctrineEventListeners($config, $container, $builder);
    }
}
