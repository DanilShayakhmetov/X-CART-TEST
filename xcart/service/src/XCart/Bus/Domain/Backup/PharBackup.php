<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Domain\Backup;

use PharData;
use RecursiveIteratorIterator;
use Silex\Application;
use XCart\Bus\System\FilesystemInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class PharBackup implements BackupInterface
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
     * @var PharData
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
        $this->rootDir    = $rootDir;
        $this->backupDir  = $backupDir;
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

        $fullPath = $this->backupDir . $backup->getTarFileName();

        $this->fileSystem->mkdir($this->backupDir);
        $this->fileSystem->remove($fullPath);

        $backup->backup = new PharData($fullPath);

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
        $backup->backup   = new PharData($this->backupDir . $backup->getTarFileName());

        return $backup;
    }

    /**
     * @param string|\Iterator $file
     */
    public function addReplaceRecord($file)
    {
        if ($file instanceof \Iterator) {
            $this->backup->buildFromIterator($file, $this->rootDir);

        } elseif (is_string($file)) {
            $this->backup->addFile($this->rootDir . $file, $file);
        }
    }

    /**
     * @param string $file
     */
    public function addCreateRecord($file)
    {
        $metadata   = $this->backup->getMetadata() ?: [];
        $metadata[] = $file;

        $this->backup->setMetadata($metadata);
    }

    /**
     * @return string[]
     */
    public function getCreated()
    {
        return $this->backup->getMetadata();
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->getTarFileName();
    }

    /**
     * @return array
     */
    public function getContentList()
    {
        $result = [];

        foreach (new RecursiveIteratorIterator($this->backup) as $file) {
            $fullPath = (string) $file->getPathName();

            $result[$this->getRelativePath($fullPath)] = $fullPath;
        }

        return $result;
    }

    /**
     * @return string
     */
    private function getTarFileName()
    {
        if ($this->scriptId === null) {
            return '';
        }

        return $this->scriptId . '.tar';
    }

    /**
     * @param string $fullPath
     *
     * @return string
     */
    private function getRelativePath($fullPath)
    {
        $tarFileName = $this->getTarFileName();

        return substr($fullPath, strpos($fullPath, $tarFileName) + strlen($tarFileName . '/'));
    }
}
