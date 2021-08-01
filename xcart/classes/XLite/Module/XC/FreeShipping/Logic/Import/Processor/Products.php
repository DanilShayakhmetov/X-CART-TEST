<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\FreeShipping\Logic\Import\Processor;

/**
 * Decorate import processor
 */
class Products extends \XLite\Logic\Import\Processor\Products implements \XLite\Base\IDecorator
{
    // {{{ Columns

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

    protected function initialize()
    {
        parent::initialize();

        $rawRows = $this->collectRawRows();

        if (
            $this->isVerification()
            && in_array('freeShipping', $rawRows[0])
        ) {
            $this->importer->getOptions()->displayFreeShippingUpdateNotification = true;
        }
    }

    // }}}

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'PRODUCT-SHIP-FOR-FREE-FMT'     => 'Wrong free shipping format',
                'PRODUCT-FREE-SHIPPING-FMT'     => 'Wrong exclude from shipping cost format',
                'PRODUCT-FREIGHT-FIXED-FEE-FMT' => 'Wrong freight fixed fee format',
            ];
    }

    /**
     * Verify 'ship for free' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyShipForFree($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('PRODUCT-SHIP-FOR-FREE-FMT', ['column' => $column,
                                                            'value'  => $value]);
        }
    }

    /**
     * Verify 'free shipping' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyFreeShipping($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('PRODUCT-FREE-SHIPPING-FMT', [
                'column' => $column,
                'value'  => $value,
            ]);
        }
    }

    /**
     * Verify 'freightFixedFee' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyFreightFixedFee($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsFloat($value)) {
            $this->addWarning('PRODUCT-FREIGHT-FIXED-FEE-FMT', [
                'column' => $column,
                'value'  => $value,
            ]);
        }
    }

    /**
     * Normalize 'freightFixedFee' value
     *
     * @param mixed $value Value
     *
     * @return float
     */
    protected function normalizeFreightFixedFeeValue($value)
    {
        return $this->normalizeValueAsFloat($value);
    }

    // }}}

    // {{{ Import

    /**
     * Import 'ship for free' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importShipForFreeColumn(\XLite\Model\Product $model, $value, array $column)
    {
        $model->setShipForFree($this->normalizeValueAsBoolean($value));
    }

    /**
     * Import 'free shipping' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importFreeShippingColumn(\XLite\Model\Product $model, $value, array $column)
    {
        $model->setFreeShip($this->normalizeValueAsBoolean($value));
    }

    // }}}
}
