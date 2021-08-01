<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\ModuleHandlers;

use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;
use XLite\Logger;

/**
 * Main
 *
 */
class Main extends \Includes\Decorator\Plugin\APlugin
{
    protected $finished = [];

    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        $registry = Manager::getRegistry();

        try {
            $moduleIds = $registry->getEnabledModuleIds();
            $sorted = Manager::sortModulesByDependency($moduleIds);

            foreach ($sorted as $moduleId) {
                if (!in_array($moduleId, $this->finished, true)) {
                    Module::callMainClassMethod($moduleId, 'updateViewListEntries');
                    $this->finished[] = $moduleId;
                }
            }
        } catch (\Exception $e) {
            Logger::getInstance()->logPostponed($e->getMessage(), LOG_ERR, $e->getTraceAsString());
        }
    }
}
