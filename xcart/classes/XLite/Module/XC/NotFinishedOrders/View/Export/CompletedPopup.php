<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\View\Export;

/**
 * Completed section
 */
class CompletedPopup extends \XLite\View\Export\CompletedPopup implements \XLite\Base\IDecorator
{
    /**
     * Get message which is shown after export
     *
     * @return string
     */
    protected function getCompleteMessage()
    {
        $message = parent::getCompleteMessage();

        if ($this->isExportHasNFO()) {
            $message = static::t('Not Finished orders were skipped during the export process. If you wish to export the orders which are now in this state, change their fulfillment status from Not Finished to any other.');
        }

        return $message;
    }
}
