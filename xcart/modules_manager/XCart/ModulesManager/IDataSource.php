<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\ModulesManager;

/**
 * Interface IDataSource
 */
interface IDataSource
{
    /**
     * Enable module
     *
     * @param string $key Module key
     */
    public function enableModule($key);

    /**
     * Disable module
     *
     * @param string $key Module key
     */
    public function disableModule($key);

    /**
     * Install module
     *
     * @param string $key Module key
     * @param array  $data
     */
    public function installModule($key, array $data);

    /**
     * Install module
     *
     * @param string $key Module key
     * @param array  $data
     */
    public function updateModule($key, array $data);

    /**
     * Renew module
     *
     * @param string $key Module key
     */
    public function renewModule($key);

    /**
     * Remove module
     *
     * @param string $key Module key
     */
    public function removeModule($key);

    /**
     * List installed modules
     *
     * @return array
     */
    public function getModulesList();

    /**
     * List installed modules
     *
     * @param string $key Module key
     *
     * @return array
     */
    public function getModule($key);
}