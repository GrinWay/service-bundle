<?php

namespace GrinWay\Service\Tests\Unit\StringService;

use GrinWay\Service\Service\StringService;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(StringService::class)]
class StringServiceGetPathTest extends AbstractStringServiceTestCase
{
    public function testNoPathsAtAllReturnsEmptyString()
    {
        $path = StringService::getPath();

        $this->assertSame('', $path);
    }

    public function testPreservesLeadSlash()
    {
        $path = StringService::getPath('/path', 'part');

        $this->assertSame('/path/part', $path);
    }

    public function testTrimsMiddleShales()
    {
        $path = StringService::getPath('/path', '////part1////', '/////////part2///part3', '//part4///');

        $this->assertSame('/path/part1/part2///part3/part4', $path);
    }

    public function testTrimsRightSlashes()
    {
        $path = StringService::getPath('/path', '////part1////', '/////////part2///part3///\\\\');

        $this->assertSame('/path/part1/part2///part3', $path);
    }
}
