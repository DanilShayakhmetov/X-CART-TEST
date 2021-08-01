<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Domain;

use BadMethodCallException;
use Silex\Application;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use UnexpectedValueException;
use XCart\Bus\Core\Archive\ArchiveFactory;
use XCart\Bus\Exception\PackageException;
use XCart\Bus\System\Filesystem;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class Package
{
    public const HASH_FILE = '.hash';

    /**
     * @var string
     */
    private $packagesDir;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var ArchiveFactory
     */
    private $archiveFactory;

    /**
     * @var Module
     */
    private $module;

    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    /**
     * @param Application        $app
     * @param Filesystem         $fileSystem
     * @param ArchiveFactory     $archiveFactory
     * @param ModuleInfoProvider $moduleInfoProvider
     */
    public function __construct(
        Application $app,
        Filesystem $fileSystem,
        ArchiveFactory $archiveFactory,
        ModuleInfoProvider $moduleInfoProvider
    ) {
        $this->packagesDir        = $app['config']['module_packs_dir'];
        $this->rootDir            = $app['config']['root_dir'];
        $this->fileSystem         = $fileSystem;
        $this->moduleInfoProvider = $moduleInfoProvider;
        $this->archiveFactory     = $archiveFactory;
    }

    /**
     * @param Module $module
     *
     * @return self
     */
    public function fromModule(Module $module): self
    {
        $package = clone $this;

        $package->module = $module;

        return $package;
    }

    /**
     * @param string $filePath
     *
     * @return self
     * @throws PackageException
     */
    public function fromFile($filePath): ?self
    {
        try {
            $phar     = new \PharData($filePath);
            $metadata = $phar->getMetadata();
            if (!$metadata) {
                throw PackageException::fromNonFormatArchive();
            }
            $module  = Module::fromPackageMetadata($metadata);
            $package = $this->fromModule($module);

            $fileName = "{$module->id}.{$module->version}";

            foreach ($package->archiveFactory->getPacker()->getAvailableExtensions() as $ext) {
                $packagePath = "{$this->packagesDir}{$fileName}{$ext}";
                $this->fileSystem->remove($packagePath);
            }

            $packageExt = $package->archiveFactory->getPacker()->getExtension($filePath);
            $packagePath = "{$this->packagesDir}{$fileName}{$packageExt}";

            $this->fileSystem->mkdir($this->packagesDir);
            $this->fileSystem->remove($packagePath);
            $this->fileSystem->rename($filePath, $packagePath, true);

            return $package;

        } catch (UnexpectedValueException $e) {
            $this->fileSystem->remove($filePath);
            throw PackageException::fromNonPharArchive();

        } catch (BadMethodCallException $e) {
            throw PackageException::fromGenericError($e);
        }
    }

    /**
     * @return Module
     */
    public function getModule(): Module
    {
        return $this->module;
    }

    /**
     * @return string
     */
    public function createPackage(): string
    {
        if ($this->module === null) {
            return '';
        }

        $iterator = $this->getIterator();
        $fullPath = $this->packagesDir . $this->getFileName();

        $this->fileSystem->mkdir($this->packagesDir);
        $this->fileSystem->remove($fullPath);

        $files    = array_keys(iterator_to_array($iterator));
        $hash     = json_encode($this->getHash($iterator));
        $metadata = $this->module->toPackageMetadata();

        if ($this->archiveFactory->getPacker()->pack($fullPath, $this->rootDir, $files, $hash, $metadata)) {
            return $fullPath;
        }

        return '';
    }

    /**
     * @param string $file
     *
     * @return self
     * @throws PackageException
     */
    public function loadPackage($file): self
    {
        if (!$file) {
            throw PackageException::fromNoFile();
        }

        return $this->fromFile($file);
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        $fileName = $this->module
            ? $this->module->id . '.' . $this->module->version
            : '';

        return $fileName . ($this->archiveFactory->getPacker()->canCompress() ? '.tgz' : '.tar');
    }

    /**
     * @return array
     */
    public function getModuleFilesList(): array
    {
        return $this->getFilesStructure(
            $this->getIterator()
        );
    }

    /**
     * @return \AppendIterator
     */
    private function getIterator(): \AppendIterator
    {
        $result = new \AppendIterator();

        $moduleInfo = $this->moduleInfoProvider->getModuleInfo($this->module->id);

        if (isset($moduleInfo['directories'])) {
            foreach ((array) $moduleInfo['directories'] as $directory) {
                if (is_dir($directory)) {
                    $result->append(new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
                    ));
                }
            }
        }

        return $result;
    }

    /**
     * @param \Iterator $iterator
     *
     * @return array
     */
    private function getHash(\Iterator $iterator): array
    {
        $files = $this->getFilesStructure($iterator);

        return array_map('md5_file', $files);
    }

    /**
     * @param \Iterator $iterator
     *
     * @return array
     */
    private function getFilesStructure(\Iterator $iterator): array
    {
        $result = [];

        foreach ($iterator as $filePath => $fileInfo) {
            if (strpos($fileInfo->getFilename(), '._') === 0) {
                continue;
            }

            $path = $this->fileSystem->makePathRelative(dirname($filePath), $this->rootDir);

            $result[$path . basename($filePath)] = $filePath;
        }

        return $result;
    }
}
