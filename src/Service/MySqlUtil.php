<?php

namespace GrinWay\Service\Service;

use SensitiveParameter;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\InputStream;
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
        protected readonly string         $databaseName,
        protected readonly string         $databasePort,
        protected readonly string         $databaseUsername,
    )
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * API
     *
     * Makes a backup by the configured absolute database dir of this bundle
     */
    public function backup(#[SensitiveParameter] string $databasePassword): bool
    {
        $filename = $this->serviceLocator->get('carbonFI')->now()->format('T d-m-Y');
        $filename = \sprintf(
            '%s.sql',
            $this->serviceLocator->get('slugger')->slug($filename),
        );
        $absFilepath = StringService::getPath(
            $this->backupAbsPath,
            $filename,
        );

        $mysqlDumpName = 'mysqldump';
        $mysqlDumpName = (new ExecutableFinder())->find($mysqlDumpName, default: $mysqlDumpName);

        $process = Process::fromShellCommandline(\sprintf(
            '"%s" --user "%s" --port "%s" --databases "%s" --skip-comments --password > "%s"',
            $mysqlDumpName,
            $this->databaseUsername,
            $this->databasePort,
            $this->databaseName,
            $absFilepath,
        ));

        $input = new InputStream();
        $input->write($databasePassword);
        $process->setInput($input);

        $this->filesystem->mkdir($this->backupAbsPath);
        $process->mustRun();
        $input->close();

//        $output = $process->getOutput();
//        if (empty($output)) {
//            return false;
//        } else {
//            $this->filesystem->dumpFile($absFilepath, $output);
//            return true;
//        }

        return true;
    }
}
