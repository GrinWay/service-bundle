<?php

namespace GrinWay\Service\Tests\Unit\Doctrine;

use GrinWay\Service\Entity\Test;
use GrinWay\Service\EventListener\Doctrine\DateTimeToUtcBeforeToDatabaseEventListener;
use GrinWay\Service\Factory\TestFactory;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DateTimeToUtcBeforeToDatabaseEventListener::class)]
class DateTimeToUtcBeforeToDatabaseEventListenerTest extends AbstractDoctrineTestCase
{
    private static Test $testEntityMoscowUpdatedAt;
    private static Test $testEntityWarsawUpdatedAt;
    private static Test $testEntityHobartUpdatedAt;
    private static \DateTimeInterface $nowMoscowTz;
    private static \DateTimeInterface $nowWarsawTz;
    private static \DateTimeInterface $nowHobartTz;

    protected function setUp(): void
    {
        parent::setUp();

        self::$nowMoscowTz = self::removeAboveSeconds(new \DateTimeImmutable('Europe/Moscow'));
        self::$nowWarsawTz = self::removeAboveSeconds(new \DateTimeImmutable('Europe/Warsaw'));
        self::$nowHobartTz = self::removeAboveSeconds(new \DateTimeImmutable('Australia/Hobart'));

        self::$testEntityMoscowUpdatedAt = TestFactory::createOne(['dateTime' => (clone self::$nowMoscowTz)])->_real();
        self::$testEntityWarsawUpdatedAt = TestFactory::createOne(['dateTime' => (clone self::$nowWarsawTz)])->_real();
        self::$testEntityHobartUpdatedAt = TestFactory::createOne(['dateTime' => (clone self::$nowHobartTz)])->_real();

        self::$em->clear();
    }

    public function testEntityWithMoscowTimezonePersistedWithUtcTimezone()
    {
        $persistedDateTime = self::removeAboveSeconds(self::$testEntityMoscowUpdatedAt->getDateTime());
        $nowUtc = (clone self::$nowMoscowTz)->setTimezone(new \DateTimeZone('UTC'));

        $this->assertSame(
            $nowUtc->format(self::FULL_DATE_TIME_FORMAT),
            $persistedDateTime->format(self::FULL_DATE_TIME_FORMAT),
        );
    }

    public function testEntityWithWarsawTimezonePersistedWithUtcTimezone()
    {
        $persistedDateTime = self::removeAboveSeconds(self::$testEntityWarsawUpdatedAt->getDateTime());
        $nowUtc = (clone self::$nowWarsawTz)->setTimezone(new \DateTimeZone('UTC'));

        $this->assertSame(
            $nowUtc->format(self::FULL_DATE_TIME_FORMAT),
            $persistedDateTime->format(self::FULL_DATE_TIME_FORMAT),
        );
    }

    public function testEntityWithHobartTimezonePersistedWithUtcTimezone()
    {
        $persistedDateTime = self::removeAboveSeconds(self::$testEntityHobartUpdatedAt->getDateTime());
        $nowUtc = (clone self::$nowHobartTz)->setTimezone(new \DateTimeZone('UTC'));

        $this->assertSame(
            $nowUtc->format(self::FULL_DATE_TIME_FORMAT),
            $persistedDateTime->format(self::FULL_DATE_TIME_FORMAT),
        );
    }

    public function testEntityWithHobartTimezoneUpdatedWithHobartTimezoneAndFlushedWithUtcTimezone()
    {
        self::$em->find(Test::class, self::$testEntityHobartUpdatedAt->getId())->setDateTime(self::$nowHobartTz);
        self::$em->flush();
        self::$em->clear();
        /** FORCE CLEAR STATE TO GET ENTITY FROM DB AGAIN (not from identity map) */
        $entityFromDb = self::$em->find(Test::class, self::$testEntityHobartUpdatedAt->getId());

        $updatedDateTime = self::removeAboveSeconds($entityFromDb->getDateTime());
        $nowUtc = (clone self::$nowHobartTz)->setTimezone(new \DateTimeZone('UTC'));

        $this->assertSame(
            $nowUtc->format(self::FULL_DATE_TIME_FORMAT),
            $updatedDateTime->format(self::FULL_DATE_TIME_FORMAT),
        );
    }

    public function testEntityWithMoscowTimezoneUpdatedWithMoscowTimezoneAndFlushedWithUtcTimezone()
    {
        self::$em->find(Test::class, self::$testEntityMoscowUpdatedAt->getId())->setDateTime(self::$nowMoscowTz);
        self::$em->flush();
        self::$em->clear();
        /** FORCE CLEAR STATE TO GET ENTITY FROM DB AGAIN (not from identity map) */
        $entityFromDb = self::$em->find(Test::class, self::$testEntityMoscowUpdatedAt->getId());

        $updatedDateTime = self::removeAboveSeconds($entityFromDb->getDateTime());
        $nowUtc = (clone self::$nowMoscowTz)->setTimezone(new \DateTimeZone('UTC'));

        $this->assertSame(
            $nowUtc->format(self::FULL_DATE_TIME_FORMAT),
            $updatedDateTime->format(self::FULL_DATE_TIME_FORMAT),
        );
    }

    public function testEntityWithWarsawTimezoneUpdatedWithWarsawTimezoneAndFlushedWithUtcTimezone()
    {
        self::$em->find(Test::class, self::$testEntityWarsawUpdatedAt->getId())->setDateTime(self::$nowWarsawTz);
        self::$em->flush();
        self::$em->clear();
        /** FORCE CLEAR STATE TO GET ENTITY FROM DB AGAIN (not from identity map) */
        $entityFromDb = self::$em->find(Test::class, self::$testEntityWarsawUpdatedAt->getId());

        $updatedDateTime = self::removeAboveSeconds($entityFromDb->getDateTime());
        $nowUtc = (clone self::$nowWarsawTz)->setTimezone(new \DateTimeZone('UTC'));

        $this->assertSame(
            $nowUtc->format(self::FULL_DATE_TIME_FORMAT),
            $updatedDateTime->format(self::FULL_DATE_TIME_FORMAT),
        );
    }
}
