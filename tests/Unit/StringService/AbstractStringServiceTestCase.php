<?php

namespace GrinWay\Service\Tests\Unit\StringService;

use GrinWay\Service\Service\StringService;
use GrinWay\Service\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Filesystem\Path;

#[CoversClass(StringService::class)]
class AbstractStringServiceTestCase extends AbstractUnitTestCase
{
    protected static StringService $stringService;
    protected static string $dynamicAbsModelFilepath;
    protected static string $dynamicPublicModelFilepathWithoutPrefix;
    protected static string $urlPrefix;
    protected static string $defaultStaticAbsFilepath;
    protected static string $dynamicPublicDir;
    protected static string $dynamicAbsDir;
    protected static string $nonExistentDynamicRelFilepath = 'nonexistentfile.txt';
    protected static string $defaultStaticPublicFilepath;

    protected function setUp(): void
    {
        parent::setUp();

        self::$urlPrefix = self::$appUrl;

        self::$stringService = self::getContainer()->get(StringService::class);

        self::$defaultStaticPublicFilepath = 'tests/static/default.txt';
        self::$defaultStaticAbsFilepath = \sprintf(
            '%s/%s',
            self::$kernelCacheDir,
            self::$defaultStaticPublicFilepath,
        );

        self::$dynamicPublicDir = 'dynamic';
        self::$dynamicAbsDir = \sprintf(
            '%s/%s',
            self::$kernelCacheDir,
            self::$dynamicPublicDir,
        );
        self::$dynamicPublicModelFilepathWithoutPrefix = \sprintf(
            '%s/%s',
            self::$dynamicPublicDir,
            Model::getTestRelFilepath(),
        );
        self::$dynamicAbsModelFilepath = \sprintf(
            '%s/%s',
            self::$kernelCacheDir,
            self::$dynamicPublicModelFilepathWithoutPrefix,
        );

        $this->removeNonExistendDynamicFilename();

        $this->touchDynamicModelFilepath();
    }

    protected function tearDown(): void
    {
        $this->removeDynamicModelFilepath();
    }

    protected function getModel(): Model
    {
        return new Model();
    }

    private function touchDynamicModelFilepath(): void
    {
        \mkdir(Path::getDirectory(self::$dynamicAbsModelFilepath), recursive: true);
        \touch(self::$dynamicAbsModelFilepath);
    }

    private function removeDynamicModelFilepath(): void
    {
        \unlink(self::$dynamicAbsModelFilepath);
    }

    private function removeNonExistendDynamicFilename()
    {
        $nonExistentDynamicFilename = \sprintf(
            '%s/%s/%s',
            self::$kernelCacheDir,
            self::$dynamicPublicDir,
            self::$nonExistentDynamicRelFilepath,
        );
        if (\is_file($nonExistentDynamicFilename)) {
            \unlink($nonExistentDynamicFilename);
        }
    }
}

class Model
{
    public static function getTestRelFilepath(): string
    {
        return 'test/test.txt';
    }
}
