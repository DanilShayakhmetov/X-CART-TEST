<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Rebuild\Executor;

use Includes\Decorator\Utils\CacheInfo;
use Includes\Decorator\Utils\CacheManager;
use Includes\Utils\Module\Manager;

class StartRebuildExecutor implements IRebuildExecutor
{
    public function isApplicable($payloadData)
    {
        return isset($payloadData['modules_list']) && is_array($payloadData['modules_list']);
    }

    public function execute($payloadData, $rebuildId)
    {
        if ($payloadData['modules_list']) {
            Manager::saveModulesToStorage($payloadData['modules_list'], $payloadData['integrated_list'] ?? []);
        }

        CacheManager::setCacheRebuildMark();
        CacheInfo::remove();

        return [
            'state'                => 'finished',
            CacheManager::KEY_NAME => CacheManager::getCacheRebuildMark(),
        ];
    }
}