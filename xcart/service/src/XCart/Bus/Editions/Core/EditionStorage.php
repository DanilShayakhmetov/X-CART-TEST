<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Editions\Core;


use Silex\Application;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\System\FilesystemInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * Class EditionStorage
 *
 * @Service\Service()
 */
class EditionStorage
{
    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * @var InstalledModulesDataSource
     */
    private $installedModulesDataSource;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;
    /**
     * @var string
     */
    private $cacheDir;
    /**
     * @var string
     */
    private $editionsUrl;

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * @param Application                $app
     * @param CoreConfigDataSource       $coreConfigDataSource
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param FilesystemInterface        $filesystem
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        CoreConfigDataSource $coreConfigDataSource,
        InstalledModulesDataSource $installedModulesDataSource,
        FilesystemInterface $filesystem
    ) {
        return new self(
            $coreConfigDataSource,
            $installedModulesDataSource,
            $filesystem,
            $app['config']['cache_dir'],
            $app['config']['marketplace.editions_url']
        );
    }

    /**
     * @param CoreConfigDataSource       $coreConfigDataSource
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param FilesystemInterface        $filesystem
     * @param string                     $cacheDir
     * @param                            $editionsUrl
     */
    public function __construct(
        CoreConfigDataSource $coreConfigDataSource,
        InstalledModulesDataSource $installedModulesDataSource,
        FilesystemInterface $filesystem,
        $cacheDir,
        $editionsUrl
    ) {
        $this->installedModulesDataSource   = $installedModulesDataSource;
        $this->coreConfigDataSource = $coreConfigDataSource;
        $this->filesystem = $filesystem;
        $this->cacheDir = $cacheDir;
        $this->editionsUrl = $editionsUrl;
    }

    /**
     * @param $name
     *
     * @return array
     */
    public function retrieveEditionByName($name)
    {
        $edition = $this->getEdition($name);

        return $edition['modules'];
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getChangeUnits($name)
    {
        $edition = $this->retrieveEditionByName($name);

        $states = [];
        foreach ($edition as $moduleId => $desiredState) {
            $desiredState = strtolower($desiredState);
            $newState = [
                'id'      => $moduleId,
            ];

            $installedData = $this->installedModulesDataSource->find($moduleId);
            if ($desiredState === 'e') {
                $newState['enable'] = true;

                if (!$installedData) {
                    $newState['install'] = true;
                    $newState['installLatestVersion'] = true;
                }
            } elseif ($desiredState === 'd') {
                $newState['enable'] = false;

                if (!$installedData) {
                    $newState['install'] = true;
                    $newState['installLatestVersion'] = true;
                } elseif ($installedData['canDisable'] === false) {
                    unset($newState['enable']);
                    $newState['remove'] = true;
                }

            } elseif ($desiredState === 'u') {
                $newState['remove'] = true;
            }

            $states[] = $newState;
        }

        return $states;
    }

    /**
     * @param $name
     *
     * @return array
     */
    public function getEdition($name)
    {
        $editions = $this->getEditions();

        if(!isset($editions[$name])) {
            throw new \UnexpectedValueException("Edition " . $name . " was not found");
        }

        return $editions[$name];
    }

    /**
     * @return array
     */
    public function getEditions()
    {
        $editions = $this->getEditionsList();

        $parser = $this->getParser($editions);

        $modulesData = $parser->getEditions('en');

        foreach ($editions as $name => &$edition) {
            $edition['modules'] = $modulesData[$name];
        }

        return $editions;
    }

    /**
     * @param $editions
     *
     * @return EditionsParser
     */
    protected function getParser($editions)
    {
        $path = $this->cacheDir .'/editions.yaml';

        try {
            $this->renewEditions($path);
        } catch (\Exception $exception) {}

        return new EditionsParser($path, array_keys($editions));
    }

    /**
     * @param string $path
     */
    protected function renewEditions($path)
    {
        $currentTime = time();
        $ttl = 86400;
        $pathRenewTime = $this->cacheDir .'/.editions_renew_time';

        $isThereTheCache = $this->filesystem->exists($path);
        $isFileExpired = true;

        if ($this->filesystem->exists($pathRenewTime)) {
            $renewTime = file_get_contents($pathRenewTime);

            $isFileExpired = $currentTime >= $renewTime;
        }

        if (!$isThereTheCache
            || $isFileExpired
        ) {
            $this->filesystem->copy(
                $this->editionsUrl,
                $path,
                true
            );
            $this->filesystem->dumpFile(
                $pathRenewTime,
                $currentTime + $ttl
            );
        }
    }

    /**
     * @return array
     */
    protected function getEditionsList()
    {
        return [
            'default' => [
                'modules'       => [],
            ],
            'multivendor' => [
                'modules'       => [],
            ],
            'ultimate' => [
                'modules'       => [],
            ],
        ];
    }
}
