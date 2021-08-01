<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\View;

/**
 * Popup export
 */
class PopupExport extends \XLite\View\PopupExport implements \XLite\Base\IDecorator
{
    /**
     * Get inner widget class name
     *
     * @return string
     */
    protected function getInnerWidget()
    {
        $result = parent::getInnerWidget();

        if ($this->isExportHasOnlyNFO()) {
            $result = 'XLite\Module\XC\NotFinishedOrders\View\Export\OnlyNFOSelected';
        }

        return $result;
    }

}
