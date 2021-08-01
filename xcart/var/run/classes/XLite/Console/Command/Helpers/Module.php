<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\Helpers;

use XLite\Core\Marketplace\Normalizer\MarketplaceModules;
use XLite\Core\Marketplace\QueryRegistry;
use XLite\Core\Marketplace\Retriever;

class Module
{
    const NOT_FOUND     = 'not_found';
    const NOT_INSTALLED = 'not_installed';
    const INSTALLED     = 'installed';
    const ENABLED       = 'enabled';

    /**
     * @param $name
     *
     * @return string
     * @throws \Exception
     */
    public function getModuleStateByName($name)
    {
        if (substr_count($name, '\\') + 1 !== 2) {
            throw new \Exception("Module name $name has wrong format. Should be Author\\\\Name");
        }

        list($author, $name) = explode('\\', $name);

        $modulesList = Retriever::getInstance()->retrieve(
            QueryRegistry::getQuery('marketplace_modules', ['includeIds' => [$author . '-' . $name]]),
            new MarketplaceModules()
        ) ?: [];

        $module = $modulesList[0] ?? [];

        $result = static::NOT_FOUND;

        if ($module) {
            if ($module['installed'] === false) {
                $result = static::NOT_INSTALLED;
            } else {
                $result = $module['enabled']
                    ? static::ENABLED
                    : static::INSTALLED;
            }
        }

        return $result;
    }
}
