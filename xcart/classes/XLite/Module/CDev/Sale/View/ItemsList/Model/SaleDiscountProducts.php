<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\ItemsList\Model;

/**
 * Sale discount products items list
 */
class SaleDiscountProducts extends \XLite\View\ItemsList\Model\Table
{
    const PARAM_SALE_DISCOUNT_ID = 'sale_discount_id';

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            ['sale_discount']
        );
    }

    /**
     * Get top actions
     *
     * @return array
     */
    protected function getTopActions()
    {
        $actions = parent::getTopActions();
        $actions[] = 'modules/CDev/Sale/sale_discount_products/parts/create.twig';

        return $actions;
    }

    /**
     * Define the URL for popup product selector
     *
     * @return string
     */
    protected function getRedirectURL()
    {
        return $this->buildURL(
            'sale_discount',
            'add_products',
            [
                'id' => $this->getSaleDiscountId(),
            ]
        );
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'productSku' => array(
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('SKU'),
                static::COLUMN_ORDERBY  => 100,
            ),
            'productName' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Product'),
                static::COLUMN_NO_WRAP  => false,
                static::COLUMN_MAIN     => true,
                static::COLUMN_ORDERBY  => 200,
                static::COLUMN_LINK    => 'product',
            ),
            'productPrice' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Price'),
                static::COLUMN_TEMPLATE => 'modules/CDev/Sale/sale_discount_products/parts/info.price.twig',
                static::COLUMN_ORDERBY  => 300,
            ),
            'productAmount' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Stock'),
                static::COLUMN_ORDERBY  => 400,
            ),
        );
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        switch ($column[static::COLUMN_LINK]) {
            case 'product':
                $result = \XLite\Core\Converter::buildURL(
                    $column[static::COLUMN_LINK],
                    '',
                    array('product_id' => $entity->getProduct()->getProductId())
                );
                break;

            default:
                $result = parent::buildEntityURL($entity, $column);
                break;
        }

        return $result;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\CDev\Sale\Model\SaleDiscountProduct';
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
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget()
    {
        return 'sale_discount';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_SALE_DISCOUNT_ID => new \XLite\Model\WidgetParam\TypeInt(
                'sale discount ID ', $this->getSaleDiscountId(), false
            ),
        );
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' sale_discount_products';
    }

    /**
     * Check - sticky panel is visible or not
     *
     * @return boolean
     */
    protected function isPanelVisible()
    {
        return true;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->sale_discount_id = $this->getParam(static::PARAM_SALE_DISCOUNT_ID);

        return $result;
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        return array_merge(
            parent::getFormParams(),
            array(
                'id' => $this->getSaleDiscountId(),
            )
        );
    }

    /**
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\CDev\Sale\View\StickyPanel\SaleDiscountProducts';
    }
}
