<?php

namespace GrinWay\Service\Tests\Unit\Doctrine\Type;

use GrinWay\Service\Doctrine\Type\Percent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Percent::class)]
class PercentTest extends TestCase
{
    public function testGetPercentOf()
    {
        $percent = new Percent(100.00);
        $source = 20.00;
        $this->assertSame(20.00, $percent->getPercentOf($source));

        $percent->setPercent(50.00);
        $this->assertSame(10.00, $percent->getPercentOf($source));

        $percent->setPercent(25.05);
        $this->assertSame(5.01, $percent->getPercentOf($source));

        $percent->setPercent(1.15);
        $this->assertSame(0.23, $percent->getPercentOf($source));
    }
}
