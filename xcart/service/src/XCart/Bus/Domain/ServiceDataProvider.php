<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Domain;

use Silex\Application;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ServiceDataProvider
{
    /**
     * @var array
     */
    private $serviceData = [];

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param Application $app
     *
     * @return static
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app
    ) {
        return new self(
            $app['config']['root_dir']
        );
    }

    /**
     * @param string $rootDir
     */
    public function __construct(
        string $rootDir
    ) {
        $this->rootDir = $rootDir;
    }

    /**
     * @param string $author
     * @param string $name
     *
     * @return array|null
     */
    public function getModuleServiceData(string $author, string $name): ?array
    {
        $path = $this->rootDir . 'classes/XLite/Module/' . $author . '/' . $name . '/service.yaml';

        return $this->getServiceData($path);
    }

    /**
     * @return array|null
     */
    public function getCoreServiceData(): ?array
    {
        $path = $this->rootDir . 'service/src/service.yaml';

        return $this->getServiceData($path);
    }

    /**
     * @param string $path
     *
     * @return array|null
     */
    public function getServiceData(string $path): ?array
    {
        if (!isset($this->serviceData[$path])) {
            try {
                $this->serviceData[$path] = Yaml::parseFile($path);
            } catch (ParseException $exception) {
                $this->serviceData[$path] = [];
            }
        }

        return $this->serviceData[$path];
    }
}
