<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Domain;

use AppendIterator;
use DirectoryIterator;
use FilesystemIterator;
use Iterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Silex\Application;
use XCart\Bus\IntegrityCheck\CoreIteratorBuilder;
use XCart\Bus\System\Filesystem;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ModuleFilesFactory
{
    /**
     * @var array
     */
    private static $skinModel = [
        'admin'    => ['admin' => []],
        'customer' => ['customer' => []],
        'console'  => ['console' => []],
        'mail'     => ['mail' => ['customer', 'common', 'admin']],
        'common'   => ['common' => []],
        'pdf'      => ['pdf' => ['customer', 'common', 'admin']],
    ];

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var CoreIteratorBuilder
     */
    private $coreIteratorBuilder;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Application         $app
     * @param CoreIteratorBuilder $coreIteratorBuilder
     * @param Filesystem          $filesystem
     *
     * @return static
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        CoreIteratorBuilder $coreIteratorBuilder,
        Filesystem $filesystem
    ) {
        return new self(
            $app['config']['root_dir'],
            $coreIteratorBuilder,
            $filesystem
        );
    }

    /**
     * @param string              $rootDir
     * @param CoreIteratorBuilder $coreIteratorBuilder
     * @param Filesystem          $filesystem
     */
    public function __construct(
        $rootDir,
        CoreIteratorBuilder $coreIteratorBuilder,
        Filesystem $filesystem
    ) {
        $this->rootDir             = $rootDir;
        $this->coreIteratorBuilder = $coreIteratorBuilder;
        $this->filesystem          = $filesystem;
    }

    /**
     * @param string $moduleId
     *
     * @return array
     */
    public function getModuleFilesIterator($moduleId, array $skins = []): array
    {
        if ($moduleId === 'CDev-Core') {
            $result = $this->coreIteratorBuilder->getIterator();
        } else {
            $result = new AppendIterator();

            foreach ($this->getDirectories($moduleId, $skins) as $directory) {
                if (is_dir($directory)) {
                    $result->append(new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
                    ));
                }
            }
        }

        return $this->getFilesStructure($result);
    }

    /**
     * @param string $moduleId
     * @param array  $skins
     *
     * @return array
     */
    public function getDirectories($moduleId, array $skins = []): array
    {
        if ($moduleId === 'XC-Service') {
            return [$this->rootDir . '/service'];
        }

        [$author, $name] = Module::explodeModuleId($moduleId);

        $classesDir    = $this->rootDir . 'classes/XLite/Module/' . $author . '/' . $name . '/';
        $templatesDirs = $this->getTemplatesDirectories($moduleId);
        $skinsDirs     = $this->getSkinsDirectories($skins);

        return array_merge([$classesDir], $templatesDirs, $skinsDirs);
    }

    /**
     * @param string $moduleId
     *
     * @return array
     */
    private function getTemplatesDirectories($moduleId): array
    {
        $result = [];
        foreach (self::$skinModel as $interface => $model) {
            foreach ($model as $dir => $innerDirs) {
                $basePaths = $this->findFoldersStartingWithName($this->rootDir . 'skins', $dir);

                if ($innerDirs) {
                    foreach ($basePaths as $basePath) {
                        foreach ($innerDirs as $innerDir) {
                            $basePaths = array_merge($basePaths, $this->findFoldersStartingWithName($basePath, $innerDir));
                        }
                    }
                }

                $result = array_merge($result, $basePaths);
            }
        }

        [$author, $name] = Module::explodeModuleId($moduleId);
        $modulePath = $author . '/' . $name . '/';

        return array_reduce($result, static function ($acc, $item) use ($modulePath) {
            $path = $item . 'modules/' . $modulePath;
            if (is_dir($path)) {
                $acc[] = $path;
            }

            return $acc;
        }, []);
    }

    /**
     * @param array $skins
     *
     * @return array
     */
    private function getSkinsDirectories(array $skins): array
    {
        $result = [];
        foreach ($skins as $interface => $dirs) {
            foreach ($dirs as $dir) {
                $realDir = $this->rootDir . 'skins/' . $dir . '/';
                if (is_dir($realDir)) {
                    $result[] = $realDir;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $root
     * @param string $name
     *
     * @return array
     */
    private function findFoldersStartingWithName($root, $name): array
    {
        $result = [];
        foreach (new DirectoryIterator($root) as $fileInfo) {
            if (!$fileInfo->isDot()
                && $fileInfo->isDir()
                && ($fileInfo->getFilename() === $name || 0 === strpos($fileInfo->getFilename(), $name . '_'))
            ) {
                $result[] = $fileInfo->getPathname() . '/';
            }
        }

        return $result;
    }

    /**
     * @param Iterator $iterator
     *
     * @return array
     */
    private function getFilesStructure(Iterator $iterator): array
    {
        $result = [];

        foreach ($iterator as $filePath => $fileInfo) {
            $path = $this->filesystem->makePathRelative(dirname($filePath), $this->rootDir);

            $result[$path . basename($filePath)] = $filePath;
        }

        return $result;
    }
}
