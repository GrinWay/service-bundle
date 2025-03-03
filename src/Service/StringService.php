<?php

namespace GrinWay\Service\Service;

use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Filesystem\Path;

/**
 * Tool for manipulating with strings
 *
 * See "API" section right below to understand what this service can
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class StringService
{
    private const SLASHES = '/\\';
    public const NORMALIZED_SLASH = '/';

    public function __construct(
        protected readonly ServiceLocator $serviceLocator,
    )
    {
    }

    /**
     * API
     *
     * Gets the complete path
     */
    public static function getPath(string...$pathParts): string
    {
        $path = '';
        foreach ($pathParts as $i => $pathPart) {
            if (0 === $i) {
                $pathPart = \rtrim($pathPart, self::SLASHES);
            } else {
                $pathPart = \trim($pathPart, self::SLASHES);
            }
            $path .= \sprintf(
                '%s' . self::NORMALIZED_SLASH,
                $pathPart,
            );
        }
        $path = \rtrim($path, self::SLASHES);
        return Path::normalize($path);
    }

    /**
     * SAFE ABSOLUTE FILEPATH GETTER
     * Safe because it falls back to the default abs filepath
     *
     * @param callable $modelDynamicRelFilepathGetter Guaranteed that model is not null
     *
     * @return string Absolute filepath with the following logic:
     * 1) Return default static abs filepath if (model is null) OR (dynamic abs filepath is not a file)
     * 2) Return dynamic abs filepath otherwise
     */
    public static function getSafeAbsFilepath(
        ?object  $model,
        string   $defaultStaticAbsFilepath,
        string   $dynamicAbsDir,
        callable $modelDynamicRelFilepathGetter,
    ): string
    {
        if (null === $model) {
            return $defaultStaticAbsFilepath;
        }

        $modelDynamicRelPath = $modelDynamicRelFilepathGetter($model);
        if (null !== $modelDynamicRelPath) {
            $modelAbsDynamicFilename = static::getPath(
                $dynamicAbsDir,
                $modelDynamicRelPath,
            );
            if (\is_file($modelAbsDynamicFilename)) {
                return $modelAbsDynamicFilename;
            }
        }

        return $defaultStaticAbsFilepath;
    }

    /**
     * SAFE PUBLIC FILEPATH GETTER (using Packages Symfony service)
     * Safe because it falls back to the default public filepath
     *
     * @param callable $modelDynamicRelFilepathGetter Guaranteed that model is not null
     * @param ?string $modelAbsPrefixToDynamicPublic Usually you will always be bypassing this parameter
     *
     * @return string Url public filepath with the following logic:
     * 1) Return default static public filepath if (model is null) OR (dynamic absolute version of public filepath is not a file)
     * 2) Return dynamic public filepath otherwise
     */
    public function getSafePublicFilepath(
        ?object  $model,
        string   $defaultStaticPublicFilepath,
        string   $dynamicPublicDir,
        callable $modelDynamicRelFilepathGetter,
        ?string  $urlPrefix = null,
        ?string  $modelAbsPrefixToDynamicPublic = null,
    ): string
    {
        /** @var Packages $asset */
        $asset = $this->serviceLocator->get('asset');

        /** @var string $kernelPublicDir */
        $modelAbsPrefixToDynamicPublic ??= $this->serviceLocator->get('kernelPublicDir');

        $defaultStaticPublicFilepath = $asset->getUrl($defaultStaticPublicFilepath);
        if (null !== $urlPrefix) {
            $defaultStaticPublicFilepath = static::getPath(
                $urlPrefix,
                $defaultStaticPublicFilepath,
            );
        }
        if (null === $model) {
            return $defaultStaticPublicFilepath;
        }

        $dynamicRelFilepath = $modelDynamicRelFilepathGetter($model);
        if (null !== $dynamicRelFilepath) {
            $modelAbsDynamicFilename = static::getPath(
                $modelAbsPrefixToDynamicPublic,
                $dynamicPublicDir,
                $dynamicRelFilepath,
            );
            if (\is_file($modelAbsDynamicFilename)) {
                $modelPublicDynamicFilename = $asset->getUrl(
                    static::getPath(
                        $dynamicPublicDir,
                        $dynamicRelFilepath,
                    ),
                );
                if (null !== $urlPrefix) {
                    $modelPublicDynamicFilename = static::getPath(
                        $urlPrefix,
                        $modelPublicDynamicFilename,
                    );
                }
                return $modelPublicDynamicFilename;
            }
        }

        return $defaultStaticPublicFilepath;
    }
}
