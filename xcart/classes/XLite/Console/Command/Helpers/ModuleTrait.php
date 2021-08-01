<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\Helpers;

trait ModuleTrait
{
    private $moduleHelper;

    /**
     * @param $name
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getModuleStateByName($name)
    {
        if (!$this->moduleHelper) {
            $this->moduleHelper = new Module();
        }

        return $this->moduleHelper->getModuleStateByName($name);
    }

    public function getMessageByState($state, $name)
    {
        switch ($state) {
            case Module::NOT_INSTALLED:
                $result = "Module $name is not installed";
                break;
            case Module::INSTALLED:
                $result = "Module $name is not enabled";
                break;
            case Module::ENABLED:
                $result = "Module $name. Everything is ok";
                break;
            default:
                $result = "Module $name does not exists. Please check the spelling";
                break;
        }

        return $result;
    }
}
