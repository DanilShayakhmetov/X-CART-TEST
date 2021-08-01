<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\FreeShipping\Logic\Export\Step;

/**
 * Decorate export product class
 */
 class Products extends \XLite\Module\XC\ProductTags\Logic\Export\Step\Products implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['shipForFree'] = [];
        $columns['freeShipping'] = [];
        $columns['freightFixedFee'] = [];

        return $columns;
    }

    /**
     * Get column value for 'freeShipping' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return boolean
     */
    protected function getShipForFreeColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->isShipForFree();
    }

    /**
     * Get column value for 'freeShipping' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getFreeShippingColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'freeShip');
    }

    /**
     * Get column value for 'freeShipping' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getFreightFixedFeeColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'freightFixedFee');
    }
}
