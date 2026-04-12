<?php

namespace GrinWay\Service\Tests\Unit\Doctrine\Function;

use GrinWay\Service\Doctrine\Function\LeaveOnlyNumbers;
use GrinWay\Service\Test\Entity\Test;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LeaveOnlyNumbers::class)]
class DateIntervalToSecDoctrineFunctionTest extends AbstractDoctrineFunctionTestCase
{
    public function testPositiveInterval()
    {
        $dql = \sprintf(
            'SELECT DATEINTERVAL_TO_SEC(t.dateinterval) AS interval_sec FROM %s t WHERE t.text = \'positive\'',
            Test::class,
        );
        $query = static::$em->createQuery($dql);
        $results = $query->getResult();

        $this->assertNotEmpty($results);
        $this->assertGreaterThan(0, \count($results));
        $this->assertEquals($results[0]['interval_sec'], static::$positiveDateInterval->total('seconds'));
    }

    public function testNegativeInterval()
    {
        $dql = \sprintf(
            'SELECT DATEINTERVAL_TO_SEC(t.dateinterval) AS interval_sec FROM %s t WHERE t.text = \'negative\'',
            Test::class,
        );
        $query = static::$em->createQuery($dql);
        $results = $query->getResult();

        $this->assertNotEmpty($results);
        $this->assertGreaterThan(0, \count($results));
        $this->assertEquals($results[0]['interval_sec'], static::$negativeDateInterval->total('seconds'));
    }
}
