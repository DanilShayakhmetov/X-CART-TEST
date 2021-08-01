<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\ItemsList\Model\Product\Admin;

use XLite\Core\CommonCell;
use XLite\Core\Request;
use XLite\Module\QSL\CloudSearch\Main;
use XLite\Module\QSL\CloudSearch\Model\Repo\Product;
use XLite\View\ItemsList\Product\Customer\Search as SearchList;


/**
 * Search product
 */
 class Search extends \XLite\View\ItemsList\Model\Product\Admin\SearchAbstract implements \XLite\Base\IDecorator
{
    /**
     * Return params list to use for search
     *
     * @return CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        if ($this->isLoadingWithCloudSearch($result)) {
            $result->{Product::P_LOAD_PRODUCTS_WITH_CLOUD_SEARCH} = true;
        }

        return $result;
    }

    /**
     * Check if product list should be loaded with CloudSearch
     *
     * @param CommonCell $cnd
     *
     * @return bool
     */
    protected function isLoadingWithCloudSearch(CommonCell $cnd)
    {
        return Main::isConfigured()
            && Main::isAdminSearchEnabled()
            && ($cnd->{Product::P_SUBSTRING} !== '' && $cnd->{Product::P_SUBSTRING} !== null);
    }
}