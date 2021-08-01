<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Core;

/**
 * Layout
 */
class Layout extends \XLite\Core\Layout implements \XLite\Base\IDecorator
{
    /**
     * Define the pages where first sidebar will be hidden.
     *
     * @return array
     */
    protected function getSidebarFirstHiddenTargets()
    {
        return array_merge(
            parent::getSidebarFirstHiddenTargets(),
            [
                'order_messages',
                'conversation',
            ]
        );
    }

    /**
     * Hide right sidebar for 'amazon_checkout' target
     *
     * @return array
     */
    protected function getSidebarSecondHiddenTargets()
    {
        return array_merge(
            parent::getSidebarSecondHiddenTargets(),
            [
                'order_messages',
                'conversation',
            ]
        );
    }
}
