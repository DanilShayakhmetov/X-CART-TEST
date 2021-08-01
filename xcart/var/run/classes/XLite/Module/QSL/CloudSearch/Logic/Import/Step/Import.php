<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Logic\Import\Step;

use XLite\Core\Database;
use XLite\Module\QSL\CloudSearch\Core\IndexingEvent\IndexingEventListener;


/**
 * Import step
 */
 class Import extends \XLite\Module\XC\CustomProductTabs\Logic\Import\Step\Import implements \XLite\Base\IDecorator
{
    /**
     * Initialize
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $tmpVar = Database::getRepo('XLite\Model\TmpVar');

        $tmpVar->setVar('csImportStarted', LC_START_TIME);
    }

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        parent::finalize();

        IndexingEventListener::triggerLatestChangesReindex();
    }
}
