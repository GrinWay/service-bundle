<?php

namespace GrinWay\Service\Tests\Unit;

use GrinWay\Service\Service\MySqlUtil;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MySqlUtil::class)]
class MySqlUtilTest extends AbstractUnitTestCase
{
    protected static string $backupAbsFilepath;
    protected static string $databaseTestPassword;
    protected static MySqlUtil $mysqlUtil;

    protected function setUp(): void
    {
        parent::setUp();

        self::$databaseTestPassword = self::getContainer()->getParameter('grinway_service.test.database.password');

        self::$backupAbsFilepath = self::getContainer()->getParameter('grinway_service.database.backup_abs_path');
        if (\is_file(self::$backupAbsFilepath)) {
            \unlink(self::$backupAbsFilepath);
        }
        $this->assertFileDoesNotExist(self::$backupAbsFilepath);

        self::$mysqlUtil = self::getContainer()->get('grinway_service.mysql_util');
    }

    public function testBackupNotToFileJustReturnResultOfTheDump()
    {
        $dump = self::$mysqlUtil->backup(self::$databaseTestPassword, toFile: false);

        $this->assertFileDoesNotExist(self::$backupAbsFilepath);
        $this->assertNotFalse($dump);
        $this->assertNotEmpty($dump);
    }

    public function testBackupToFileReturnResultOfTheDump()
    {
        $dump = self::$mysqlUtil->backup(self::$databaseTestPassword, toFile: true);

        $this->assertFileExists(self::$backupAbsFilepath);
        $this->assertFileIsReadable(self::$backupAbsFilepath);
        $this->assertNotFalse($dump);
        $this->assertNotEmpty($dump);
    }
}
