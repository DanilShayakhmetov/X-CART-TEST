<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Logic\Export\Step;

use XLite\Core\Database;
use XLite\Model\Membership;
use XLite\Module\CDev\Wholesale\Model\Base\AWholesalePrice;

/**
 * Products
 */
abstract class Products extends \XLite\Logic\Export\Step\Products implements \XLite\Base\IDecorator
{
    const ALL_CUSTOMERS_TIER = 'All customers';

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['wholesalePrices'] = [];
        $columns['minimumPurchaseQuantity'] = [];
        $columns['applySaleToWholesale'] = [];

        return $columns;
    }

    /**
     * Get column value for 'applySaleToWholesale' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getApplySaleToWholesaleColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'applySaleToWholesale');
    }

    /**
     * Get column value for 'wholesalePrices' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getWholesalePricesColumnValue(array $dataset, $name, $i)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Module\CDev\Wholesale\Model\Repo\WholesalePrice::P_PRODUCT} = $dataset['model'];

        return $this->convertWholesalePrices(
            \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')->search($cnd)
        );
    }

    /**
     * Get column value for 'minimumPurchaseQuantity' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getMinimumPurchaseQuantityColumnValue(array $dataset, $name, $i)
    {
        $product = $dataset['model'];
        return array_merge(
            ['All customers=' . $product->getMinQuantity()],
            array_map(function (Membership $membership) use ($product) {
                return sprintf(
                    '%s=%s',
                    $this->formatMembershipModel($membership),
                    $product->getMinQuantity($membership)
                );
            }, Database::getRepo('XLite\Model\Membership')->findAll())
        );
    }

    /**
     * Get Wholesale prices
     *
     * @param array $prices
     *
     * @return array
     */
    protected function convertWholesalePrices(array $prices)
    {
        $result = [];

        /** @var \XLite\Module\CDev\Wholesale\Model\Base\AWholesalePrice $price */
        foreach ($prices as $price) {
            $str = $price->getQuantityRangeBegin();

            if (0 < $price->getQuantityRangeEnd()) {
                $str .= '-' . $price->getQuantityRangeEnd();
            }

            if ($price->getMembership()) {
                $str .= '(' . $this->formatMembershipModel($price->getMembership()) . ')';
            }

            $str .= '=' . $price->getPrice();

            if ($price->getType() === AWholesalePrice::WHOLESALE_TYPE_PERCENT) {
                $str .= '%';
            }

            $result[] = $str;
        }

        return $result;
    }
}
