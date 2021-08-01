<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Rebuild;

use Includes\Utils\FileManager;
use XLite\Core\Config;
use XLite\Core\Database;

class HookContext
{
    /**
     * Performs the flush if yaml parsing is successful
     * @param $file
     */
    public function loadYaml($file)
    {
        if (FileManager::isFileReadable($file)) {
            Database::getInstance()->loadFixturesFromYaml($file);
        }
    }

    public function createConfigOption($params)
    {
        return $this->getRepo(\XLite\Model\Config::class)->createOption($params);
    }

    public function getConfig()
    {
        return Config::getInstance();
    }

    public function getSystemOptions($category, $option)
    {
        return \XLite::getInstance()->getOptions([$category, $option]);
    }

    public function getEM()
    {
        return Database::getEM();
    }

    public function getRepo($modelName)
    {
        return Database::getRepo($modelName);
    }

    /**
     * @param string $key
     */
    public function getInternalState($key)
    {
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setInternalState($key, $value)
    {
    }
}