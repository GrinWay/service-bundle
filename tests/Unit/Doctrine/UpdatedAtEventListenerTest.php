<?php

namespace GrinWay\Service\Tests\Unit\Doctrine;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use GrinWay\Service\EventListener\Doctrine\UpdatedAtEventListener;
use GrinWay\Service\Test\Entity\Test;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpdatedAtEventListener::class)]
class UpdatedAtEventListenerTest extends AbstractDoctrineTestCase
{
    public function testUpdatedAtWasNotSetWhenEntityFlushedForTheFirstTimeAndWasAutomaticallySetWhenPreUpdate()
    {
        $testEntity = $this->getTestEntity();
        $testEntity->setUpdatedAt(null);
        $this->assertNull($testEntity->getUpdatedAt());
        self::$em->persist($testEntity);
        self::$em->flush();
        self::$em->clear();
        $existingTestEntity = self::$em->find(Test::class, $testEntity->getId());
        $updatedAt = $existingTestEntity->getUpdatedAt();

        $this->assertNull($updatedAt);

        $existingTestEntity->setDateTime(null);
        self::$em->flush();
        self::$em->clear();
        $updatedAt = self::$em->find(Test::class, $existingTestEntity->getId())->getUpdatedAt();

        $this->assertTrue($updatedAt instanceof \DateTimeImmutable);
        $this->assertEquals(
            0,
            (int)CarbonImmutable::make($updatedAt)->diff(Carbon::now('UTC'))->total('seconds'),
            message: 'Date time diff with (int)casting must be 0 second (updated at assigned right now)'
        );
    }
}
