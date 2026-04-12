<?php

namespace GrinWay\Service\Service;

use SensitiveParameter;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Service for managing databases
 *
 * See "API" sections right below to see what this can
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class MySqlUtil
{
    private readonly Filesystem $filesystem;

    public function __construct(
        protected readonly ServiceLocator $serviceLocator,
        protected readonly string         $backupAbsPath,
        protected readonly string         $databaseHost,
        protected readonly string         $databasePort,
        protected readonly string         $databaseName,
        protected readonly string         $databaseUsername,
    )
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * API
     *
     * Makes a backup by the configured absolute database dir of this bundle
     *
     * @return string|false Tries to return full dump of the backup, but there is a possibility return false
     * when something went wrong
     */
    public function backup(#[SensitiveParameter] string $databasePassword, bool $toFile = true, string $format = 'Y-m-d H:i:s T'): string|false
    {
        $filename = $this->serviceLocator->get('carbonFI')->now()->format($format);
        $filename = \sprintf(
            '%s.sql',
            $this->serviceLocator->get('slugger')->slug($filename),
        );
        $absFilepath = StringService::getPath(
            $this->backupAbsPath,
            $filename,
        );

        $mysqlExecutableName = 'mysqldump';
        $mysqlExecutableName = (new ExecutableFinder())->find($mysqlExecutableName, default: $mysqlExecutableName);

        $mysqlDumpCommand = '"%s" --user "%s" --host %s --port "%s" --databases "%s" --skip-comments';

        $dopCommandParameters = [];
        if (true === $toFile) {
            $this->filesystem->mkdir($this->backupAbsPath);
            $mysqlDumpCommand .= ' ' . '--result-file "%s"';
            $dopCommandParameters[] = $absFilepath;
        }

        $process = Process::fromShellCommandline(\sprintf(
            $mysqlDumpCommand,
            $mysqlExecutableName,
            $this->databaseUsername,
            $this->databaseHost,
            $this->databasePort,
            $this->databaseName,
            ...$dopCommandParameters,
        ), env: [
            'MYSQL_PWD' => $databasePassword,
        ]);

// Unfortunately InputStream it's a risky decision (sometimes doesn't work)
//        $input = new InputStream();
//        $input->write($databasePassword);
//        $process->setInput($input);

        $process->mustRun();
//        $input->close();

        if (true === $toFile) {
            return \file_get_contents($absFilepath);
        }

        $output = $process->getOutput();
        if (empty($output)) {
            return false;
        }

        return $output;
    }
}
