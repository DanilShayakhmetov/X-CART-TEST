<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\ItemsList\Product\Customer;

use XLite\View\CacheableTrait;

/**
 * Sale discount products list
 *
 * @ListChild (list="center")
 */
class SaleDiscount extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    use CacheableTrait;

    const PARAM_ID = 'id';

    /**
     * Return target to retrive this widget from AJAX
     *
     * @return string
     */
    protected static function getWidgetTarget()
    {
        return 'sale_discount';
    }

    /**
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' sale-discount-products';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\Module\CDev\Sale\View\Pager\Customer\ControllerPager';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ID => new \XLite\Model\WidgetParam\TypeInt('Sale discount ID', $this->getSaleDiscountId()),
        ];
    }

    /**
     * Get widget parameters
     *
     * @return array
     */
    protected function getWidgetParameters()
    {
        $list = parent::getWidgetParameters();
        $list[static::PARAM_ID] = $this->getSaleDiscountId();

        return $list;
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = static::PARAM_ID;
    }

    /**
     * Default search conditions
     *
     * @param  \XLite\Core\CommonCell $searchCase Search case
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        $searchCase = parent::postprocessSearchCase($searchCase);
        $searchCase->{\XLite\Module\CDev\Sale\Model\Repo\Product::P_SALE_DISCOUNT} = $this->getSaleDiscount();

        return $searchCase;
    }

    /**
     * Auxiliary method to check visibility
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * Check if header is visible
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return $this->hasResults();
    }

    /**
     * Check if pager is visible
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return $this->hasResults();
    }

    /**
     * getEmptyListTemplate
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return 'items_list' . LC_DS . $this->getEmptyListFile();
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getEmptyListDescription()
    {
        return static::t('Sorry, no products have been added to this discount offer.', ['homePageUrl' => \XLite::getInstance()->getShopURL()]);
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getSaleDiscount()
            && $this->getSaleDiscount()->getShowInSeparateSection()
            && $this->getSaleDiscount()->isActive();
    }

    /**
     * Return name of the session cell identifier
     *
     * @return string
     */
    public function getSessionCell()
    {
        return parent::getSessionCell() . $this->getSaleDiscountId();
    }

    /**
     * @return int|null
     */
    protected function getSaleDiscountId()
    {
        $controller = \XLite::getController();
        if ($controller instanceof \XLite\Module\CDev\Sale\Controller\Customer\SaleDiscount) {
            return $controller->getSaleDiscountId();
        }

        return null;
    }

    /**
     * @return int|null
     */
    protected function getSaleDiscount()
    {
        $controller = \XLite::getController();
        if ($controller instanceof \XLite\Module\CDev\Sale\Controller\Customer\SaleDiscount) {
            return $controller->getSaleDiscount();
        }

        return null;
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();
        $list[] = $this->getSaleDiscountId();

        return $list;
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-sale-discount-products';
    }
}
