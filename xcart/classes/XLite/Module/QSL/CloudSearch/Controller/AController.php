<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Controller;

use XLite;
use XLite\Module\QSL\CloudSearch\Core\IndexingEvent\IndexingEventProfiler;
use XLite\Module\QSL\CloudSearch\Core\RegistrationScheduler;
use XLite\Module\QSL\CloudSearch\Core\ServiceApiClient;
use XLite\Module\QSL\CloudSearch\Main;

/**
 * Abstract controller
 */
abstract class AController extends \XLite\Controller\AController implements \XLite\Base\IDecorator
{
    /**
     * Handles the request.
     * Parses the request variables if necessary. Attempts to call the specified action function
     *
     * @return void
     */
    public function handleRequest()
    {
        if (
            $this->getTarget() !== 'cloud_search_api'
            && !XLite::isCacheBuilding()
        ) {
            $scheduler = RegistrationScheduler::getInstance();

            $apiClient = new ServiceApiClient();

            if ($scheduler->isScheduled()) {
                // Registration scheduled after running install.php and after cache rebuild
                if (XLite::isAdminZone() || !Main::isConfigured()) {
                    $apiClient->register();

                    $scheduler->unschedule();
                }
            }
        }

        parent::handleRequest();
    }

    public function __destruct()
    {
        IndexingEventProfiler::getInstance()->log();
    }
}
