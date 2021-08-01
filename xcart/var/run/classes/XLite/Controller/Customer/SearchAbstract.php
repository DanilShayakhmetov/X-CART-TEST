<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

use XLite\Controller\Features\ItemsListControllerTrait;
use XLite\Core\Session;
use XLite\View\ItemsList\Product\Customer\Search as ProducsSearch;

/**
 * Products search
 */
abstract class SearchAbstract extends \XLite\Controller\Customer\ACustomer
{
    use ItemsListControllerTrait {
        getCondition as protected getConditionTrait;
    }

    /**
     * Return items list class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return '\XLite\View\ItemsList\Product\Customer\Search';
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function mapSearchConditionsFromRequest()
    {
        $sessionSearchConditions = [];

        // Fill search conditions from requst
        $className = $this->getItemsListClass();
        $searchConditionsRequestNames = $className::getSearchParams();
        $data = $this->prepareSearchData();
        foreach ($searchConditionsRequestNames as $name => $condition) {
            if (isset($data[$condition])) {
                $sessionSearchConditions[$condition] = $data[$condition];
            }
        }

        return $sessionSearchConditions;
    }

    /**
     * Return 'checked' attribute for parameter.
     *
     * @param string $paramName Name of parameter
     * @param mixed  $value     Value to check with OPTIONAL
     *
     * @return string
     */
    public function getChecked($paramName, $value = 'Y')
    {
        return $value === $this->getCondition($paramName) ? 'checked' : '';
    }

    public function getCondition($paramName)
    {
        return $this->getSearchValuesStorage()->getValue($paramName);
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Search');
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('Search results');
    }

    protected function doNoAction()
    {
        Session::getInstance()->{$this->getSessionCellName()} = array_merge(
            $this->prepareSearchData(),
            $this->getSessionSearchConditions()
        );

        parent::doNoAction();
    }

    /**
     * doActionSearch
     *
     * @return void
     */
    protected function doActionSearch()
    {
        Session::getInstance()->{$this->getSessionCellName()} = $this->prepareSearchData();
        $this->doActionSearchItemsList();

        $urlParams = ['mode' => 'search'];

        if (\XLite\Core\Request::getInstance()->substring) {
            $urlParams['substring'] = \XLite\Core\Request::getInstance()->substring;
        }

        $this->setReturnURL($this->buildURL('search', '', $urlParams));
    }

    /**
     * @return array
     */
    protected function prepareSearchData()
    {
        $searchParams = ProducsSearch::getSearchParams();
        $advancedParams = array_diff(ProducsSearch::getSearchParams(), ProducsSearch::getBasicSearchParams());

        $productsSearch = [];

        $cBoxFields = [
            ProducsSearch::PARAM_SEARCH_IN_SUBCATS,
        ];

        foreach ($searchParams as $modelParam => $requestParam) {
            if (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $productsSearch[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        foreach ($cBoxFields as $requestParam) {
            $productsSearch[$requestParam] = isset(\XLite\Core\Request::getInstance()->$requestParam)
                ? 1
                : 0;
        }

        $defaults = [
            ProducsSearch::PARAM_INCLUDING         => 'all',
            ProducsSearch::PARAM_SEARCH_IN_SUBCATS => 'Y',
        ];

        $productsSearch = array_merge(
            $defaults,
            $productsSearch
        );

        Session::getInstance()->{$this->getAdvancedPanelCellName()} = array_intersect(array_keys($productsSearch), array_values($advancedParams));

        return $productsSearch;
    }

    /**
     * Checks session var and returns true, if advanced panel should be shown
     *
     * @return boolean
     */
    public function showAdvancedPanel()
    {
        return Session::getInstance()->{$this->getAdvancedPanelCellName()};
    }

    /**
     * Return session var name related to advanced search panel
     *
     * @return string
     */
    public function getAdvancedPanelCellName()
    {
        return 'show_advanced_search_panel';
    }

}
