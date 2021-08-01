<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Product\Customer;

/**
 * Search
 *
 */
abstract class SearchAbstract extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    use \XLite\View\ItemsList\Product\Customer\DefaultSortByTrait;

    /**
     * Widget param names
     */
    const PARAM_SUBSTRING         = 'substring';
    const PARAM_CATEGORY_ID       = 'categoryId';
    const PARAM_SEARCH_IN_SUBCATS = 'searchInSubcats';
    const PARAM_INCLUDING         = 'including';
    const PARAM_BY_TITLE          = 'by_title';
    const PARAM_BY_DESCR          = 'by_descr';
    const PARAM_BY_SKU            = 'by_sku';

    /**
     * Allowed sort criteria
     */
    const SORT_BY_MODE_DEFAULT = 'p.product_id';

    /**
     * Widget target
     */
    const WIDGET_TARGET = 'search';

    /**
     * Get default sort order value
     *
     * @return string
     */
    protected function getDefaultSortOrderValue()
    {
        return \XLite\Core\Config::getInstance()->General->default_search_sort_order;
    }

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = array())
    {
        parent::__construct($params);

        $this->processAdditionalSortByModes();
    }

    /**
     * Get products single order 'sort by' fields
     * Return in format [sort_by_field => sort_order]
     *
     * @return array
     */
    protected function getSingleOrderSortByFields()
    {
        return parent::getSingleOrderSortByFields() + [
            static::SORT_BY_MODE_DEFAULT => static::SORT_ORDER_DESC
        ];
    }

    /**
     * Defines if the widget is replacing history state
     *
     * @return boolean
     */
    protected function getReplaceState()
    {
        return true;
    }

    /**
     * Return search parameters.
     * :TODO: refactor
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return array(
            \XLite\Model\Repo\Product::P_SUBSTRING   => self::PARAM_SUBSTRING,
            \XLite\Model\Repo\Product::P_CATEGORY_ID => self::PARAM_CATEGORY_ID,
            \XLite\Model\Repo\Product::P_INCLUDING   => self::PARAM_INCLUDING,
            \XLite\Model\Repo\Product::P_BY_TITLE    => self::PARAM_BY_TITLE,
            \XLite\Model\Repo\Product::P_BY_DESCR    => self::PARAM_BY_DESCR,
            \XLite\Model\Repo\Product::P_BY_SKU      => self::PARAM_BY_SKU,
        );
    }

    /**
     * Return only basic search parameters. They are excluded from list of more search options
     *
     * @return array
     */
    public static function getBasicSearchParams()
    {
        return array(
            \XLite\Model\Repo\Product::P_SUBSTRING         => self::PARAM_SUBSTRING,
            \XLite\Model\Repo\Product::P_INCLUDING         => self::PARAM_INCLUDING,
            \XLite\Model\Repo\Product::P_SEARCH_IN_SUBCATS => self::PARAM_SEARCH_IN_SUBCATS,
        );
    }

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = self::WIDGET_TARGET;

        return $result;
    }

    /**
     * Return target to retrive this widget from AJAX
     *
     * @return string
     */
    protected static function getWidgetTarget()
    {
        return self::WIDGET_TARGET;
    }

    /**
     * Returns list of GET-parameters, which should be collected from form
     *
     * @return array
     */
    protected function getSearchUrlParams()
    {
        return array_merge(static::getSearchParams(), static::getBasicSearchParams());
    }

    /**
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' products-search-result';
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/search/search.css';

        return $list;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/search/controller.js';

        return $list;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getListHead()
    {
        return static::t('X products found', array('count' => $this->getItemsCount()));
    }

    /**
     * Check if head title is visible
     *
     * @return boolean
     */
    protected function isHeadVisible()
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
        return 0 < $this->getItemsCount();
    }

    /**
     * Check if pager is visible
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return 0 < $this->getItemsCount();
    }

    /**
     * isFooterVisible
     *
     * @return boolean
     */
    protected function isFooterVisible()
    {
        return true;
    }

    /**
     * Search widget must be visible always.
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return true;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Customer\Product\Search';
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        return parent::getCommonParams() + array('mode' => 'search');
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_SUBSTRING => new \XLite\Model\WidgetParam\TypeString(
                'Substring', ''
            ),
            self::PARAM_CATEGORY_ID => new \XLite\Model\WidgetParam\TypeInt(
                'Category ID', 0
            ),
            self::PARAM_INCLUDING => new \XLite\Model\WidgetParam\TypeSet(
                'Including',
                \XLite\Model\Repo\Product::INCLUDING_ALL,
                array(
                    \XLite\Model\Repo\Product::INCLUDING_ALL,
                    \XLite\Model\Repo\Product::INCLUDING_ANY,
                    \XLite\Model\Repo\Product::INCLUDING_PHRASE,
                )
            ),
            self::PARAM_BY_TITLE => new \XLite\Model\WidgetParam\TypeCheckbox(
                'Search in title', 0
            ),
            self::PARAM_BY_DESCR => new \XLite\Model\WidgetParam\TypeCheckbox(
                'Search in description', 0
            ),
            self::PARAM_BY_SKU => new \XLite\Model\WidgetParam\TypeString(
                'Search in SKU', 0
            ),
        );
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge(
            $this->requestParams,
            \XLite\View\ItemsList\Product\Customer\Search::getSearchParams()
        );
    }

    /**
     * Return params list to use for search
     * TODO refactor
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        if ('directLink' !== \XLite\Core\Config::getInstance()->General->show_out_of_stock_products) {
            $result->{\XLite\Model\Repo\Product::P_INVENTORY} = false;
        }

        return $result;
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Product')->search(
            $this->prepareCnd($cnd),
            $countOnly
        );
    }

    /**
     * Prepare search condition before search
     *
     * @param \XLite\Core\CommonCell $cnd Search condition
     *
     * @return \XLite\Core\CommonCell
     */
    protected function prepareCnd(\XLite\Core\CommonCell $cnd)
    {
        // In the Customer zone we search in subcategories always.
        $cnd->{\XLite\Model\Repo\Product::P_SEARCH_IN_SUBCATS} = 'Y';

        if ($cnd->{\XLite\Model\Repo\Product::P_INCLUDING} === null) {
            $cnd->{\XLite\Model\Repo\Product::P_INCLUDING} = 'all';
        }

        return $cnd;
    }

    /**
     * Unset 'pageId' value from saved parameters
     *
     * @param string $param Parameter name
     *
     * @return mixed
     */
    protected function getSavedRequestParam($param)
    {
        return \XLite\View\Pager\APager::PARAM_PAGE_ID !== $param ? parent::getSavedRequestParam($param) : null;
    }

    /**
     * @inheritdoc
     */
    public function displayCommentedData(array $data)
    {
        parent::displayCommentedData(array_merge($data, [
            'searchUrlParams' => $this->getSearchUrlParams()
        ]));
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return $list[] = $this->getDir() . '/search/body.twig';
    }
}
