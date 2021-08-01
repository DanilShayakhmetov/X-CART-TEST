<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\IntegrityCheck;

use Silex\Application;
use XCart\Bus\Domain\ModuleInfoProvider;
use XCart\Bus\System\Filesystem;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class CoreIteratorBuilder
{
    /**
     * @var string
     */
    private $rootDir;
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Application $app
     * @param Filesystem  $filesystem
     *
     * @return CoreIteratorBuilder
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        Filesystem $filesystem
    ) {
        return new self(
            $app['config']['root_dir'],
            $filesystem
        );
    }

    /**
     * @param string     $rootDir
     * @param Filesystem $filesystem
     */
    public function __construct(
        $rootDir,
        Filesystem $filesystem
    ) {
        $this->rootDir = $rootDir;
        $this->filesystem = $filesystem;
    }

    /**
     * Return iterator to walk through directories
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        $iterator = new \RecursiveDirectoryIterator($this->rootDir, \FilesystemIterator::SKIP_DOTS);

        $coreFilteredIterator = new CoreFilterIterator(
            $iterator,
            $this->rootDir,
            $this->filesystem,
            $this->preparePatternsList(
                $this->getMandatoryList()
            ),
            $this->preparePatternsList(
                $this->getExcludedList()
            ),
            $this->preparePatternsList(
                $this->getIncludedList()
            )
        );

        return new \RecursiveIteratorIterator(
            $coreFilteredIterator,
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
    }

    /**
     * @return array
     */
    protected function getExcludedList()
    {
        return [
            'list' => [
                'var',
                'files',
                'images',
                'sql',
                'etc' . DIRECTORY_SEPARATOR . 'config.local.php',
                'etc' . DIRECTORY_SEPARATOR . 'config.personal.php',
                'etc' . DIRECTORY_SEPARATOR . 'config.php',
                'etc' . DIRECTORY_SEPARATOR . 'config.dev.php',
                'classes' . DIRECTORY_SEPARATOR . 'XLite' . DIRECTORY_SEPARATOR . 'Module',
                'service',
                'public',
                'composer.json',
                'composer.lock',
                'LICENSE.txt',
                'LICENSE.txt.ru',
                'CLOUDSEARCHTERMS.txt',
            ],
            'raw' => [
                "skins\/.*\/modules",
                ".*\/.log",
                ".*.gitignore",
                ".*\/.git.*"
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getIncludedList()
    {
        return [
            'list'   => [
                'classes' . DIRECTORY_SEPARATOR . 'XLite' . DIRECTORY_SEPARATOR . 'Module' . DIRECTORY_SEPARATOR . 'AModule',
                'classes' . DIRECTORY_SEPARATOR . 'XLite' . DIRECTORY_SEPARATOR . 'Module' . DIRECTORY_SEPARATOR . 'AModuleSkin.php',
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getMandatoryList()
    {
        $list = array_map(
            function ($interface) {
                return 'skins' . DIRECTORY_SEPARATOR . $interface;
            },
            array_keys(ModuleInfoProvider::getSkinModel())
        );

        return ['list' => $list];
    }

    /**
     * Prepare patterns list
     *
     * @return string
     */
    protected function preparePatternsList($list)
    {
        $list = array_merge(
            ['list' => [], 'raw' => []],
            $list
        );

        $toImplode = $list['raw'];

        foreach ($list['list'] as $pattern) {
            $toImplode[] = preg_quote($pattern, '/');
        }

        return  '/^(?:' . implode('|', $toImplode) . ')/Ss';
    }
}
