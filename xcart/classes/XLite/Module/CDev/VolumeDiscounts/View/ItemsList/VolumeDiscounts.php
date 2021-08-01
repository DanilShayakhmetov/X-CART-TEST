<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\View\ItemsList;

use XLite\View\FormField\Input\PriceOrPercent;
use XLite\View\FormField\Select\AbsoluteOrPercent;
use XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount;

/**
 * Volume discounts items list
 */
class VolumeDiscounts extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Maximum numeric value for absolute discount
     */
    const MAX_NUMERIC_VALUE = 9999999999;

    /**
     * Discount keys
     *
     * @var   array
     */
    protected $discountKeys = [];

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/VolumeDiscounts/discounts/list/style.less';

        return $list;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'subtotalRangeBegin' => [
                static::COLUMN_NAME    => static::t('Subtotal'),
                static::COLUMN_LINK    => 'volume_discount',
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_ORDERBY => 100,
            ],
            'discount'           => [
                static::COLUMN_NAME    => static::t('Discount'),
                static::COLUMN_ORDERBY => 200,
            ],
            'dateRangeBegin'     => [
                static::COLUMN_NAME    => static::t('Active fromF'),
                static::COLUMN_ORDERBY => 300,
            ],
            'dateRangeEnd'       => [
                static::COLUMN_NAME    => static::t('Active tillF'),
                static::COLUMN_ORDERBY => 400,
            ],
        ];
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount';
    }

    /**
     * Preprocess subtotalRangeBegin
     *
     * @param float          $subtotalRangeBegin
     * @param array          $column
     * @param VolumeDiscount $entity
     *
     * @return string
     */
    protected function preprocessSubtotalRangeBegin($subtotalRangeBegin, $column, $entity)
    {
        return static::t('from') . ' ' . $this->formatPriceValue($subtotalRangeBegin);
    }

    /**
     * Preprocess discount
     *
     * @param array          $discount
     * @param array          $column
     * @param VolumeDiscount $entity
     *
     * @return string
     */
    protected function preprocessDiscount($discount, $column, $entity)
    {
        $discountValue = $discount[PriceOrPercent::PRICE_VALUE];
        $discountType= $discount[PriceOrPercent::TYPE_VALUE];

        return $discountType === AbsoluteOrPercent::TYPE_ABSOLUTE
            ? $this->formatPriceValue($discountValue)
            : $discountValue . AbsoluteOrPercent::getInstance()->getPercentTypeLabel();
    }

    /**
     * Format price value
     *
     * @param float                                                   $price
     *
     * @return string
     */
    protected function formatPriceValue($price) {
        return \XLite::getInstance()->getCurrency()->getPrefix() .
            \XLite::getInstance()->getCurrency()->formatValue($price) .
            \XLite::getInstance()->getCurrency()->getSuffix();
    }

    /**
     * Preprocess dateRangeBegin
     *
     * @param int            $dateRangeBegin
     * @param array          $column
     * @param VolumeDiscount $entity
     *
     * @return string
     */
    protected function preprocessDateRangeBegin($dateRangeBegin, $column, $entity)
    {
        return $dateRangeBegin
            ? $this->formatdate($dateRangeBegin)
            : '-';
    }

    /**
     * Preprocess dateRangeEnd
     *
     * @param int            $dateRangeEnd
     * @param array          $column
     * @param VolumeDiscount $entity
     *
     * @return string
     */
    protected function preprocessDateRangeEnd($dateRangeEnd, $column, $entity)
    {
        return $dateRangeEnd
            ? $this->formatdate($dateRangeEnd)
            : '-';
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildURL('volume_discount');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add discount';
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return static::t('itemslist.admin.volume_discounts.blank');
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return ['volumeDiscounts'];
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' volume-discounts';
    }

    // {{{ Data

    /**
     * Return discounts list
     *
     * @param \XLite\Core\CommonCell $cnd Search condition
     * @param boolean $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $cnd->{\XLite\Module\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount::P_ORDER_BY_MEMBERSHIP} = ['membership.membership_id', 'ASC'];
        $cnd->{\XLite\Module\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount::P_ORDER_BY_SUBTOTAL} = ['v.subtotalRangeBegin', 'ASC'];

        return \XLite\Core\Database::getRepo('XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount')
            ->search($cnd, $countOnly);
    }

    // }}}
}
