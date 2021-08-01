<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SalesTax\View\ItemsList\Model;

/**
 * Rate items list
 */
class Rate extends \XLite\View\ItemsList\Model\Table
{
    protected $sortByModes = array(
        'r.zone'       => 'Zone',
        'r.membership' => 'User membership',
        'r.taxClass'   => 'Tax class',
        'r.value'      => 'Value',
    );

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = array(
            'zone' => array(
                static::COLUMN_NAME          => static::t('Zone'),
                static::COLUMN_CLASS         => 'XLite\View\Taxes\Inline\Zone',
                static::COLUMN_ORDERBY       => 100,
                static::COLUMN_HEAD_HELP     => '<a href="' . static::buildURL('zones') . '" target="_blank">' . static::t('Manage zones') . '</a>',
                static::COLUMN_SORT          => 'r.zone',
            ),
            'membership' => array(
                static::COLUMN_NAME          => static::t('User membership'),
                static::COLUMN_CLASS         => 'XLite\View\FormField\Inline\Select\Membership',
                static::COLUMN_ORDERBY       => 300,
                static::COLUMN_SORT          => 'r.membership',
            ),
            'taxableBase' => array(
                static::COLUMN_NAME          => static::t('Taxable base'),
                static::COLUMN_CLASS         => 'XLite\Module\CDev\SalesTax\View\FormField\Inline\RateTaxableBase',
                static::COLUMN_ORDERBY       => 350,
            ),
            'value' => array(
                static::COLUMN_NAME          => static::t('Rate') . ', (%)',
                static::COLUMN_CLASS         => 'XLite\View\FormField\Inline\Input\Text\FloatInput',
                static::COLUMN_PARAMS        => array(
                    \XLite\View\FormField\Input\Text\FloatInput::PARAM_E => 4,
                ),
                static::COLUMN_ORDERBY       => 400,
                static::COLUMN_SORT          => 'r.value',
            ),
        );

        $isDefinedTaxClasses = (bool)\XLite\Core\Database::getRepo('XLite\Model\TaxClass')->findAll();

        if ($isDefinedTaxClasses) {
            $columns['taxClass'] = array(
                static::COLUMN_NAME          => static::t('Tax class'),
                static::COLUMN_CLASS         => 'XLite\View\Taxes\Inline\TaxClass',
                static::COLUMN_ORDERBY       => 200,
                static::COLUMN_SORT          => 'r.taxClass',
            );
        }

        return $columns;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\CDev\SalesTax\Model\Tax\Rate';
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('vat_sales_rate');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New sale tax rate';
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' rates';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return null;
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_NONE;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = new \XLite\Core\CommonCell;

        $result->{\XLite\Module\CDev\SalesTax\Model\Repo\Tax\Rate::PARAM_EXCL_TAXABLE_BASE}
            = \XLite\Module\CDev\SalesTax\Model\Tax\Rate::TAXBASE_SHIPPING;

        if ($this->getOrderBy()) {
            $result->{\XLite\Model\Repo\Order::P_ORDER_BY} = $this->getOrderBy();
        }

        return $result;
    }

    /**
     * Return "empty list" catalog
     *
     * @return string
     */
    protected function getEmptyListDir()
    {
        return 'modules/CDev/SalesTax/items_list';
    }

    /**
     * getEmptyListFile
     *
     * @return string
     */
    protected function getEmptyListFile()
    {
        return 'empty.twig';
    }
}
