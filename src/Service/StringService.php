<?php

namespace GrinWay\Service\Service;

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

    /**
     * API
     *
     * Gets the complete path
     */
    public static function getPath(string...$pathParts): string
    {
        $path = '';
        foreach ($pathParts as $pathPart) {
            $path .= \sprintf(
                '%s' . self::NORMALIZED_SLASH,
                \rtrim($pathPart, self::SLASHES),
            );
        }
        $path = \rtrim($path, self::SLASHES);
        return Path::normalize($path);
    }
}
