<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Dashboard\Admin\InfoBlock\Alert;

use XLite\Core\Auth;
use XLite\Core\Database;
use XLite\Model\Product as ProductModel;
use XLite\Model\Repo\Product as ProductRepo;

/**
 * @ListChild (list="dashboard.info_block.alerts", weight="100", zone="admin")
 */
class LowStockProducts extends \XLite\View\Dashboard\Admin\InfoBlock\AAlert
{
    /**
     * @return int
     */
    protected function getCounter()
    {
        return Database::getRepo(ProductModel::class)->getLowInventoryProductsAmount();
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' low-stock-products';
    }

    /**
     * @return string
     */
    protected function getIcon()
    {
        return $this->getSVGImage('images/low_stock_products.svg');
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Low stock info');
    }

    /**
     * @return string
     */
    protected function getHeaderUrl()
    {
        return $this->buildURL(
            'product_list',
            '',
            [
                ProductRepo::P_INVENTORY => ProductRepo::INV_LOW,
            ]
        );
    }

    /**
     * @return bool
     */
    protected function checkACL()
    {
        return parent::checkACL()
            && (Auth::getInstance()->hasRootAccess()
                || Auth::getInstance()->isPermissionAllowed('manage catalog'));
    }
}
