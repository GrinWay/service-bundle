<?php

namespace GrinWay\Service\Tests\Unit\Doctrine\Function;

use GrinWay\Service\Doctrine\Function\LeaveOnlyNumbers;
use GrinWay\Service\Entity\Test;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LeaveOnlyNumbers::class)]
class LeaveOnlyNumbersDoctrineFunctionTest extends AbstractDoctrineFunctionTestCase
{
    protected function setUp(): void
    {
        static::$positiveDateInterval = new \DateInterval('P12Y34M56DT78H90M09S');

        parent::setUp();
    }

    public function test()
    {
        $dql = \sprintf(
            'SELECT
                        LEAVE_ONLY_NUMBERS(t.dateinterval) AS interval_n,
                        LEAVE_ONLY_NUMBERS(t.text) AS text_n
                    FROM %s t
                ',
            Test::class,
        );
        $query = static::$em->createQuery($dql);
        $results = $query->getResult();

        $this->assertNotEmpty($results);
        $this->assertGreaterThan(0, \count($results));
        $this->assertSame($results[0]['interval_n'], '123456789009');
        $this->assertSame($results[0]['text_n'], '1209');
    }
}
