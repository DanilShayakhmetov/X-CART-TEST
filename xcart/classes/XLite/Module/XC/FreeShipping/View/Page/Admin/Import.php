<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\Page\Admin;


class Import extends \XLite\View\Page\Admin\Import implements \XLite\Base\IDecorator
{
    protected function getInnerWidget()
    {
        $widget = parent::getInnerWidget();

        if (
            $widget === 'XLite\View\Import\Completed'
            && $this->getImporter()
            && $this->getImporter()->isDisplayFreeShippingUpdateNotice()
        ) {
            $widget = '\XLite\Module\XC\FreeShipping\View\Import\FreeShippingUpdateNotification';
        }

        return $widget;
    }
}