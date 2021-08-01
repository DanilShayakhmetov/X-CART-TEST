<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core\Archive;

use Silex\Application;
use XCart\Bus\System\FilesystemInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ExecTar extends AArchive
{
    private $executable;

    private $version;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var string
     */
    private $tmpDir;

    /**
     * @var bool
     */
    private $canCompress;

    /**
     * @param Application         $app
     * @param FilesystemInterface $filesystem
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        FilesystemInterface $filesystem
    ) {
        return new self(
            $app['xc_config']['service']['tar_path'] ?? '',
            $app['config']['tmp_dir'],
            $filesystem
        );
    }

    /**
     * @param string              $executable
     * @param string              $tmpDir
     * @param FilesystemInterface $filesystem
     */
    public function __construct(
        $executable,
        $tmpDir,
        FilesystemInterface $filesystem
    ) {
        if ($executable) {
            $this->executable = $executable;

            if (!$this->isApplicable()) {
                $this->executable = null;
                $this->version    = null;
            }
        }

        $this->filesystem = $filesystem;
        $this->tmpDir     = $tmpDir;
    }

    /**
     * @return bool
     */
    public function isApplicable(): bool
    {
        return (bool) $this->getVersion();
    }

    /**
     * @param string $path
     * @param string $destination
     *
     * @return bool
     */
    public function unpack($path, $destination): bool
    {
        $compression = $this->isCompressed($path) ? 'z' : '';

        return $this->execute('', "--exclude '._*' -C {$destination} -x{$compression}f {$path}");
    }

    /**
     * @param string $path
     * @param string $root
     * @param array  $files
     * @param string $hash
     * @param array  $metadata
     *
     * @return bool
     */
    public function pack($path, $root, $files, $hash, $metadata): bool
    {
        $commpession = $this->isCompressed($path) ? 'z' : '';

        $permissions = '';

        if ($this->isGNUTar()) {
            $permissions = '--numeric-owner --owner=0 --group=0';
        } elseif ($this->isBSDTar()) {
            $permissions = '--uid=0 --uname=0 --gid=0 --gname=0';
        }

        $this->filesystem->dumpFile($root . '.hash', $hash);
        $files[] = $root . '.hash';

        $this->filesystem->dumpFile($root . '.phar/.metadata.bin', serialize($metadata));
        $files[] = $root . '.phar/.metadata.bin';

        $rootLength = strlen(rtrim($root, '/') . '/');
        $files      = array_map(static function ($file) use ($rootLength) {
            return substr($file, $rootLength);
        }, $files);

        $command = "--exclude '._*' $permissions -c{$commpession}f {$path} " . implode(" ", $files);

        $result = $this->execute($root, $command);

        if (!$result) {
            $commandWithoutPermissions = "-c{$commpession}f {$path} " . implode(" ", $files);
            $result = $this->execute($root, $commandWithoutPermissions);
        }

        $this->filesystem->remove($root . '.hash');
        $this->filesystem->remove($root . '.phar');

        return $result;
    }

    /**
     * @param string $root
     * @param string $command
     *
     * @return bool
     */
    private function execute($root, $command): bool
    {
        $executable = $this->getTarExecutable();

        if ($executable) {
            chdir($root);

            $output    = [];
            $returnVar = null;
            @exec("{$executable} {$command}", $output, $returnVar);

            return $returnVar === 0;
        }

        return false;

    }

    /**
     * @return string
     */
    private function getVersion(): string
    {
        if ($this->version === null) {
            $this->version = '';
            $executable    = $this->getTarExecutable();

            if ($executable) {
                $output = [];
                @exec("$executable --version", $output);

                $matches = [];
                if ($output && preg_match('/(bsdtar [\d.]+|tar \(GNU tar\) [\d.]+)/', $output[0], $matches)) {
                    $this->version = (string) $matches[1];
                }
            }
        }

        return $this->version;
    }

    /**
     * @return bool
     */
    private function isGNUTar(): bool
    {
        return strpos($this->getVersion(), 'GNU tar') !== false;
    }

    /**
     * @return bool
     */
    private function isBSDTar(): bool
    {
        return strpos($this->getVersion(), 'bsdtar') !== false;
    }

    /**
     * @return string
     */
    private function getTarExecutable(): string
    {
        if ($this->executable === null) {
            $this->executable = @exec('which tar') ?: 'tar';
        }

        return $this->executable;
    }

    /**
     * @return bool
     */
    public function canCompress(): bool
    {
        if ($this->canCompress === null) {
            $tempFileName = 'gzipCheck_';
            $tempFilePath = $this->filesystem->tempnam($this->tmpDir, $tempFileName);
            $tempArchivePath = "{$tempFilePath}.gz";

            @exec("gzip -c {$tempFilePath} > {$tempArchivePath}", $output, $returnVar);
            $tempFileIsCompressed = $returnVar === 0;

            $tempFilesToRemove = [$tempFilePath];
            if ($tempFileIsCompressed) {
                $tempFilesToRemove[] = $tempArchivePath;
            }
            $this->filesystem->remove($tempFilesToRemove);

            $this->canCompress = $tempFileIsCompressed;
        }

        return $this->canCompress;
    }
}
