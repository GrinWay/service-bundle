<?php

namespace GrinWay\Service\Tests\Unit\StringService;

use GrinWay\Service\Service\StringService;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(StringService::class)]
class StringServiceGetSafePublicFilenameTest extends AbstractStringServiceTestCase
{
    public function testModelIsNullDefaultStaticPublicFilepathChosenWithoutUrlPrefix()
    {
        /** @var StringService $stringService */
        $stringService = self::$stringService;

        $model = null;
        $defaultStaticPublicFilepath = self::$defaultStaticPublicFilepath;
        $dynamicPublicDir = self::$dynamicPublicDir;

        $safePublicFilepath = $stringService->getSafePublicFilepath(
            model: $model,
            defaultStaticPublicFilepath: $defaultStaticPublicFilepath,
            dynamicPublicDir: $dynamicPublicDir,
            modelDynamicRelFilepathGetter: static fn(Model $m) => Model::getTestRelFilepath(),
            modelAbsPrefixToDynamicPublic: self::$kernelCacheDir,
        );

        $expectedPublicFilepath = \sprintf(
            '%s%s',
            '/', // ASSET SYMFONY PACKAGE MUST ADD SLASH IN THE BEGINNING OF PUBLIC PATH
            $defaultStaticPublicFilepath,
        );
        $this->assertSame($expectedPublicFilepath, $safePublicFilepath);
    }

    public function testDynamicAbsFilepathDoesNotExistDefaultStaticPublicFilepathChosenWithoutUrlPrefix()
    {
        /** @var StringService $stringService */
        $stringService = self::$stringService;

        $model = $this->getModel();
        $defaultStaticPublicFilepath = self::$defaultStaticPublicFilepath;
        $dynamicPublicDir = self::$dynamicPublicDir;

        $safePublicFilepath = $stringService->getSafePublicFilepath(
            model: $model,
            defaultStaticPublicFilepath: $defaultStaticPublicFilepath,
            dynamicPublicDir: $dynamicPublicDir,
            modelDynamicRelFilepathGetter: static fn(Model $m) => self::$nonExistentDynamicRelFilepath,
            modelAbsPrefixToDynamicPublic: self::$kernelCacheDir,
        );

        $expectedPublicFilepath = \sprintf(
            '%s%s',
            '/', // ASSET SYMFONY PACKAGE MUST ADD SLASH IN THE BEGINNING OF PUBLIC PATH
            $defaultStaticPublicFilepath,
        );
        $this->assertSame($expectedPublicFilepath, $safePublicFilepath);
    }

    public function testDynamicChosenWithoutUrlPrefix()
    {
        /** @var StringService $stringService */
        $stringService = self::$stringService;

        $model = $this->getModel();
        $defaultStaticPublicFilepath = self::$defaultStaticPublicFilepath;
        $dynamicPublicDir = self::$dynamicPublicDir;

        $safePublicFilepath = $stringService->getSafePublicFilepath(
            model: $model,
            defaultStaticPublicFilepath: $defaultStaticPublicFilepath,
            dynamicPublicDir: $dynamicPublicDir,
            modelDynamicRelFilepathGetter: static fn(Model $m) => Model::getTestRelFilepath(),
            modelAbsPrefixToDynamicPublic: self::$kernelCacheDir,
        );

        $expectedPublicFilename = \sprintf(
            '%s%s',
            '/', // ASSET SYMFONY PACKAGE MUST ADD SLASH IN THE BEGINNING OF PUBLIC PATH
            self::$dynamicPublicModelFilepathWithoutPrefix,
        );
        $this->assertSame($expectedPublicFilename, $safePublicFilepath);
    }

    public function testModelIsNullDefaultStaticPublicFilepathChosenWithUrlPrefix()
    {
        /** @var StringService $stringService */
        $stringService = self::$stringService;

        $model = null;
        $defaultStaticPublicFilepath = self::$defaultStaticPublicFilepath;
        $dynamicPublicDir = self::$dynamicPublicDir;

        $safePublicFilepath = $stringService->getSafePublicFilepath(
            model: $model,
            defaultStaticPublicFilepath: $defaultStaticPublicFilepath,
            dynamicPublicDir: $dynamicPublicDir,
            modelDynamicRelFilepathGetter: static fn(Model $m) => Model::getTestRelFilepath(),
            urlPrefix: self::$urlPrefix,
            modelAbsPrefixToDynamicPublic: self::$kernelCacheDir,
        );

        $expectedPublicFilename = \sprintf(
            '%s%s%s',
            self::$urlPrefix,
            '/', // ASSET SYMFONY PACKAGE MUST ADD SLASH IN THE BEGINNING OF PUBLIC PATH
            $defaultStaticPublicFilepath,
        );
        $this->assertSame($expectedPublicFilename, $safePublicFilepath);
    }

    public function testDynamicAbsFilepathDoesNotExistDefaultStaticPublicFilepathChosenWithUrlPrefix()
    {
        /** @var StringService $stringService */
        $stringService = self::$stringService;

        $model = $this->getModel();
        $defaultStaticPublicFilepath = self::$defaultStaticPublicFilepath;
        $dynamicPublicDir = self::$dynamicPublicDir;

        $safePublicFilepath = $stringService->getSafePublicFilepath(
            model: $model,
            defaultStaticPublicFilepath: $defaultStaticPublicFilepath,
            dynamicPublicDir: $dynamicPublicDir,
            modelDynamicRelFilepathGetter: static fn(Model $m) => self::$nonExistentDynamicRelFilepath,
            urlPrefix: self::$urlPrefix,
            modelAbsPrefixToDynamicPublic: self::$kernelCacheDir,
        );

        $expectedPublicFilename = \sprintf(
            '%s%s%s',
            self::$urlPrefix,
            '/', // ASSET SYMFONY PACKAGE MUST ADD SLASH IN THE BEGINNING OF PUBLIC PATH
            $defaultStaticPublicFilepath,
        );
        $this->assertSame($expectedPublicFilename, $safePublicFilepath);
    }

    public function testDynamicChosenWithUrlPrefix()
    {
        /** @var StringService $stringService */
        $stringService = self::$stringService;

        $model = $this->getModel();
        $defaultStaticPublicFilepath = self::$defaultStaticPublicFilepath;
        $dynamicPublicDir = self::$dynamicPublicDir;

        $safePublicFilepath = $stringService->getSafePublicFilepath(
            model: $model,
            defaultStaticPublicFilepath: $defaultStaticPublicFilepath,
            dynamicPublicDir: $dynamicPublicDir,
            modelDynamicRelFilepathGetter: static fn(Model $m) => Model::getTestRelFilepath(),
            urlPrefix: self::$urlPrefix,
            modelAbsPrefixToDynamicPublic: self::$kernelCacheDir,
        );

        $expectedPublicFilename = \sprintf(
            '%s%s%s',
            self::$urlPrefix,
            '/', // ASSET SYMFONY PACKAGE MUST ADD SLASH IN THE BEGINNING OF PUBLIC PATH
            self::$dynamicPublicModelFilepathWithoutPrefix,
        );
        $this->assertSame($expectedPublicFilename, $safePublicFilepath);
    }
}
