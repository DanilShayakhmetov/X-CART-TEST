<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Domain\Backup;

use Silex\Application;
use XCart\Bus\System\FilesystemInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class DirBackup implements BackupInterface
{
    /**
     * @var string
     */
    private $backupDir;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var FilesystemInterface
     */
    private $fileSystem;

    /**
     * @var string
     */
    private $scriptId;

    /**
     * @var string
     */
    private $backup;

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
            $app['config']['module_packs_dir'],
            $app['config']['root_dir'],
            $filesystem
        );
    }

    /**
     * @param string              $backupDir
     * @param string              $rootDir
     * @param FilesystemInterface $fileSystem
     */
    public function __construct(
        $backupDir,
        $rootDir,
        FilesystemInterface $fileSystem
    ) {
        $this->backupDir  = $backupDir;
        $this->rootDir    = $rootDir;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @param string $scriptId
     *
     * @return self
     */
    public function create($scriptId)
    {
        $backup = clone $this;

        $backup->scriptId = $scriptId;

        $backup->backup = $this->backupDir . $backup->scriptId . '/';

        $this->fileSystem->remove($backup->backup);
        $this->fileSystem->mkdir($backup->backup);

        return $backup;
    }

    /**
     * @param string $scriptId
     *
     * @return self
     */
    public function load($scriptId)
    {
        $backup = clone $this;

        $backup->scriptId = $scriptId;
        $backup->backup   = $this->backupDir . $backup->scriptId . '/';

        return $backup;
    }

    /**
     * @param string|\Iterator $file
     */
    public function addReplaceRecord($file)
    {
        if ($file instanceof \Iterator) {
            /** @var \SplFileInfo $item */
            foreach ($file as $item) {
                $source      = $item->getRealPath();
                $destination = str_replace($this->rootDir, $this->backup, $source);
                $this->fileSystem->copy($source, $destination);
            }

        } elseif (is_string($file)) {
            $this->fileSystem->copy($this->rootDir . $file, $this->backup . $file);
        }
    }

    /**
     * @param string $file
     */
    public function addCreateRecord($file)
    {
        file_put_contents($this->backup . '.metadata', $file . \PHP_EOL, \FILE_APPEND);
    }

    /**
     * @return string[]
     */
    public function getCreated()
    {
        $metadata = $this->backup . '.metadata';
        if ($this->fileSystem->exists($metadata)) {
            $files = file_get_contents($metadata);

            return array_filter(explode(\PHP_EOL, $files));
        }

        return [];
    }

    /**
     * @return array
     */
    public function getContentList()
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->backup, \FilesystemIterator::SKIP_DOTS)
        );

        $result = [];
        foreach ($files as $file) {
            $path = $file->getRealPath();
            $file = str_replace($this->backup, '', $path);
            if ($file === '.metadata') {
                continue;
            }

            $result[str_replace($this->backup, '', $path)] = $path;
        }

        return $result;
    }
}
