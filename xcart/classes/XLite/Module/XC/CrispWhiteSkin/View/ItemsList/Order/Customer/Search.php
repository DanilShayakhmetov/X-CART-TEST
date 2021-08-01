<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\ItemsList\Order\Customer;

/**
 * Search
 */
class Search extends \XLite\View\ItemsList\Order\Customer\Search implements \XLite\Base\IDecorator
{
    /**
     * @return boolean
     */
    protected function isHeadVisible()
    {
        return false;
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getEmptyListDescription()
    {
        return static::t('No orders');
    }

    /**
     * getEmptyListFile
     *
     * @return string
     */
    protected function getEmptyListFile()
    {
        return '../modules/XC/CrispWhiteSkin/items_list/order/empty.twig';
    }
}
