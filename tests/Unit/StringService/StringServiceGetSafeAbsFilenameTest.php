<?php

namespace GrinWay\Service\Tests\Unit\StringService;

use GrinWay\Service\Service\StringService;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(StringService::class)]
class StringServiceGetSafeAbsFilenameTest extends AbstractStringServiceTestCase
{
    public function testModelIsNullDefaultStaticAbsFilepathChosen()
    {
        $model = null;
        $defaultStaticAbsFilepath = self::$defaultStaticAbsFilepath;
        $dynamicAbsDir = self::$dynamicAbsDir;

        $safeAbsFilepath = StringService::getSafeAbsFilepath(
            model: $model,
            defaultStaticAbsFilepath: $defaultStaticAbsFilepath,
            dynamicAbsDir: $dynamicAbsDir,
            modelDynamicRelFilepathGetter: static fn(Model $m) => Model::getTestRelFilepath(),
        );

        $this->assertSame($defaultStaticAbsFilepath, $safeAbsFilepath);
    }

    public function testDynamicAbsFilepathDoesNotExistDefaultStaticAbsFilepathChosen()
    {
        $model = $this->getModel();
        $defaultStaticAbsFilepath = self::$defaultStaticAbsFilepath;
        $dynamicAbsDir = self::$dynamicAbsDir;

        $safeAbsFilepath = StringService::getSafeAbsFilepath(
            model: $model,
            defaultStaticAbsFilepath: $defaultStaticAbsFilepath,
            dynamicAbsDir: $dynamicAbsDir,
            modelDynamicRelFilepathGetter: static fn(Model $m) => self::$nonExistentDynamicRelFilepath,
        );

        $this->assertSame($defaultStaticAbsFilepath, $safeAbsFilepath);
    }

    public function testDynamicChosen()
    {
        $model = $this->getModel();
        $defaultStaticAbsFilepath = self::$defaultStaticAbsFilepath;
        $dynamicAbsDir = self::$dynamicAbsDir;

        $safeAbsFilepath = StringService::getSafeAbsFilepath(
            model: $model,
            defaultStaticAbsFilepath: $defaultStaticAbsFilepath,
            dynamicAbsDir: $dynamicAbsDir,
            modelDynamicRelFilepathGetter: static fn(Model $m) => Model::getTestRelFilepath(),
        );

        $expectedPublicFilename = \sprintf(
            '%s%s%s',
            self::$kernelCacheDir,
            '/', // ASSET SYMFONY PACKAGE MUST ADD SLASH IN THE BEGINNING OF PUBLIC PATH
            self::$dynamicPublicModelFilepathWithoutPrefix,
        );
        $this->assertSame($expectedPublicFilename, $safeAbsFilepath);
    }
}
