<?php

namespace GrinWay\Service\Tests\Unit\Doctrine;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use GrinWay\Service\EventListener\Doctrine\CreatedAtEventListener;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreatedAtEventListener::class)]
class CreatedAtEventListenerTest extends AbstractDoctrineTestCase
{
    public function testCreatedAtSetAutomaticallyWhenPrePersist()
    {
        $testEntity = $this->getTestEntity();
        $this->assertNull($testEntity->getCreatedAt());
        self::$em->persist($testEntity);
        $createdAtAfterPersist = $testEntity->getCreatedAt();

        $this->assertTrue($createdAtAfterPersist instanceof \DateTimeImmutable);
        $this->assertEquals(
            0,
            (int)CarbonImmutable::make($createdAtAfterPersist)->diff(Carbon::now('UTC'))->total('seconds'),
            message: 'Date time diff with (int)casting must be 0 second (created at assigned right now)'
        );
    }
}
