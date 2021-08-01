<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View\ItemsList\Product\Customer;

use XLite\View\Product\ListItem;

/**
 * Search
 *
 */
class Search extends \XLite\View\ItemsList\Product\Customer\Search implements \XLite\Base\IDecorator
{
    /**
     * Get product list item widget params required for the widget of type getProductWidgetClass().
     *
     * @param \XLite\Model\Product $product
     *
     * @return array
     */
    protected function getProductWidgetParams(\XLite\Model\Product $product)
    {
        $result = parent::getProductWidgetParams($product);

        $result[ListItem::PARAM_LIST_READABLE_NAME] = 'SearchResults';

        return $result;
    }
}
