<?php

namespace GrinWay\Service\Tests\Unit\Service;

use GrinWay\Service\Service\MySqlUtil;
use GrinWay\Service\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Filesystem\Filesystem;

#[CoversClass(MySqlUtil::class)]
class MySqlUtilTest extends AbstractUnitTestCase
{
    protected static string $backupAbsDir;
    protected static string $databaseTestPassword;
    protected static MySqlUtil $mysqlUtil;

    protected function setUp(): void
    {
        parent::setUp();

        self::$databaseTestPassword = self::getContainer()->getParameter('grinway_service.test.database.password');

        self::$backupAbsDir = self::getContainer()->getParameter('grinway_service.database.backup_abs_dir');
        if (\is_dir(self::$backupAbsDir)) {
            (new Filesystem())->remove(self::$backupAbsDir);
        }
        $this->assertFileDoesNotExist(self::$backupAbsDir);

        self::$mysqlUtil = self::getContainer()->get('grinway_service.mysql_util');
    }

    public function testBackupNotToFileJustReturnResultOfTheDump()
    {
        $dump = self::$mysqlUtil->backup(self::$databaseTestPassword, toFile: false);

        $this->assertFileDoesNotExist(self::$backupAbsDir);
        $this->assertNotFalse($dump);
        $this->assertNotEmpty($dump);
    }

    public function testBackupToFileReturnResultOfTheDump()
    {
        $dump = self::$mysqlUtil->backup(self::$databaseTestPassword, toFile: true);

        $this->assertFileExists(self::$backupAbsDir);
        $this->assertFileIsReadable(self::$backupAbsDir);
        $this->assertNotFalse($dump);
        $this->assertNotEmpty($dump);
    }
}
