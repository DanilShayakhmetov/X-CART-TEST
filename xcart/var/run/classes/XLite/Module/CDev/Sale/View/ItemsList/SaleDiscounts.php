<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\ItemsList;

/**
 * Sale discounts items list
 */
class SaleDiscounts extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return static::t('itemslist.admin.sale_discounts.blank');
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Sale/sale_discounts/list/style.less';

        return $list;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'name' => array(
                static::COLUMN_NAME     => static::t('Sale name'),
                static::COLUMN_LINK     => 'sale_discount',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_MAIN     => true,
                static::COLUMN_ORDERBY  => 100,
            ),
            'value' => array(
                static::COLUMN_NAME     => static::t('Discount'),
                static::COLUMN_ORDERBY  => 300,
            ),
            'products_count' => array(
                static::COLUMN_NAME     => '',
                static::COLUMN_TEMPLATE => 'modules/CDev/Sale/sale_discounts/list/products_count.twig',
                static::COLUMN_ORDERBY  => 600,
            ),
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\CDev\Sale\Model\SaleDiscount';
    }

    /**
     * Get create message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getCreateMessage($count)
    {
        return static::t('X discount(s) has been created', array('count' => $count));
    }

    /**
     * Get remove message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getRemoveMessage($count)
    {
        return static::t('X discount(s) has been removed', array('count' => $count));
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildURL('sale_discount');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New sale';
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
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
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
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return array('sale_discounts');
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' sale_discounts';
    }

    // {{{ Data

    /**
     * Return Sales list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return \XLite\Core\Database::getRepo('XLite\Module\CDev\Sale\Model\SaleDiscount')->search($cnd, $countOnly);
    }

    // }}}

    // {{{ Content helpers

    /**
     * Define line class  as list of names
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line model OPTIONAL
     *
     * @return array
     */
    protected function defineLineClass($index, \XLite\Model\AEntity $entity = null)
    {
        $classes = parent::defineLineClass($index, $entity);
        if ($entity) {
            $classes[] = $entity->getEnabled() ? 'enabled' : 'disabled';
            $classes[] = $entity->isActive() ? 'active' : 'inactive';
        }

        return $classes;
    }

    // }}}

    // {{{ Preprocessors

    /**
     * Preprocess value for Discount column
     *
     * @param mixed                                   $value  Value
     * @param array                                   $column Column data
     * @param \XLite\Module\CDev\Sale\Model\SaleDiscount      $sale Entity
     *
     * @return string
     */
    protected function preprocessValue($value, array $column, \XLite\Module\CDev\Sale\Model\SaleDiscount $sale)
    {
        return round($value, 2) . '%';
    }

    /**
     * Preprocess value for Name column
     *
     * @param mixed                                   $value  Value
     * @param array                                   $column Column data
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $coupon Entity
     *
     * @return string
     */
    protected function preprocessName($value, array $column, \XLite\Module\CDev\Sale\Model\SaleDiscount $sale)
    {
        return htmlspecialchars($value);
    }

    // }}}

    /**
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\CDev\Sale\View\StickyPanel\SaleDiscounts';
    }
}
