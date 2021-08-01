<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart;

class ModulesManager
{
    private $dataSource;

    /**
     * @param ModulesManager\IDataSource $source
     */
    public function __construct(\XCart\ModulesManager\IDataSource $source)
    {
        $this->dataSource = $source;
    }

    /**
     * @param $key
     * @deprecated
     */
    public function enableModule($key)
    {
        $this->dataSource->enableModule($key);
    }

    /**
     * @param $key
     * @deprecated
     */
    public function disableModule($key)
    {
        $this->dataSource->disableModule($key);
    }

    /**
     * @param       $key
     * @param array $data
     * @deprecated
     */
    public function installModule($key, array $data)
    {
        $this->dataSource->installModule($key, $data);
    }

    /**
     * @param       $key
     * @param array $data
     * @deprecated
     */
    public function updateModule($key, array $data)
    {
        $this->dataSource->updateModule($key, $data);
    }

    /**
     * @param $key
     * @deprecated
     */
    public function renewModule($key)
    {
        $this->dataSource->renewModule($key);
    }

    /**
     * @param $key
     * @deprecated
     */
    public function removeModule($key)
    {
        $this->dataSource->removeModule($key);
    }

    /**
     * @return array
     */
    public function getModulesList()
    {
        return $this->dataSource->getModulesList();
    }

    /**
     * @param $key
     *
     * @return array
     */
    public function getModule($key)
    {
        return $this->dataSource->getModule($key);
    }
}