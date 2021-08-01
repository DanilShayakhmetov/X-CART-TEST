<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\View\Product\Details\Customer\Page;

/**
 * Product tabs
 */
 class Tabs extends \XLite\Module\XC\Reviews\View\Product\Details\Customer\Page\Tabs implements \XLite\Base\IDecorator
{
    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();
        $params[] = \XLite\Core\Database::getRepo('XLite\Module\XC\CustomProductTabs\Model\Product\Tab')->getVersion();
        $params[] = \XLite\Core\Database::getRepo('XLite\Module\XC\CustomProductTabs\Model\Product\CustomGlobalTab')->getVersion();

        return $params;
    }
}