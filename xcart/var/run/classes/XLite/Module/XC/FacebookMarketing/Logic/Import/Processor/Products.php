<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Logic\Import\Processor;


/**
 * Products
 */
 class Products extends \XLite\Module\XC\FreeShipping\Logic\Import\Processor\Products implements \XLite\Base\IDecorator
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

        $columns['facebookMarketingEnabled'] = [];

        return $columns;
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
        return parent::getMessages() + [
            'PRODUCT-FACEBOOK-MARKETING-ENABLED-FMT' => 'Wrong "facebook marketing enabled" format',
        ];
    }

    /**
     * Verify 'facebookMarketingEnabled' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyFreeShipping($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('PRODUCT-FACEBOOK-MARKETING-ENABLED-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    // }}}

    // {{{ Import

    /**
     * Import 'facebookMarketingEnabled' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importFacebookMarketingEnabledColumn(\XLite\Model\Product $model, $value, array $column)
    {
        $model->setFacebookMarketingEnabled($this->normalizeValueAsBoolean($value));
    }

    // }}}
}