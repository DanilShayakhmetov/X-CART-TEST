<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\CloudFilters;


use XLite;
use XLite\Model\WidgetParam\TypeBool;
use XLite\Model\WidgetParam\TypeCollection;
use XLite\Module\QSL\CloudSearch\Core\SearchParameters;
use XLite\Module\QSL\CloudSearch\Main;
use XLite\Module\QSL\CloudSearch\Model\Repo\Product as ProductRepo;
use XLite\Module\QSL\CloudSearch\Core\ServiceApiClient;
use XLite\Module\QSL\CloudSearch\Model\Repo\Product;

/**
 * Cloud filters sidebar box widget
 */
class FiltersBox extends \XLite\View\SideBarBox
{
    const PARAM_FILTER_CONDITIONS = 'filterConditions';
    const PARAM_IS_ASYNC_FILTERS = 'isAsyncFilters';
    const MAX_FOLDED_FILTER_VALUES = 10;

    /**
     * Get a list of JS files required to display the widget
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/QSL/CloudSearch/cloud_filters/filters.js';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/QSL/CloudSearch/cloud_filters/filters.less',
            'media' => 'screen',
        ];

        return $list;
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-cloud-filters';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/CloudSearch/cloud_filters/sidebar_box';
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/CloudSearch/cloud_filters/sidebar_box/container.twig';
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('Filters');
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        if (!parent::isVisible() || !Main::isConfigured()) {
            return false;
        }

        if ($this->getParam(self::PARAM_IS_ASYNC_FILTERS)) {
            return true;
        }

        $searchResults = $this->getSearchResults();

        return $searchResults !== null && !empty($searchResults['facets']);
    }

    /**
     * Get current CloudSearch search results object
     *
     * @return array
     */
    protected function getSearchResults()
    {
        $client = new ServiceApiClient();

        return $client->search(new SearchParameters($this->getParam(self::PARAM_FILTER_CONDITIONS)));
    }

    /**
     * Get current filtering conditions
     *
     * @return array|\stdClass
     */
    protected function getFilterConditions()
    {
        $conditions = $this->getParam(self::PARAM_FILTER_CONDITIONS);

        $filters = $conditions->{ProductRepo::P_CLOUD_FILTERS};

        return !empty($filters) ? $filters : new \stdClass();
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        return [
            static::RESOURCE_JS => [
                [
                    'file'      => $this->isDeveloperMode() ? 'vue/vue.js' : 'vue/vue.min.js',
                    'no_minify' => true,
                ],
            ],
        ];
    }

    /**
     * Get commented widget data
     *
     * @return array
     */
    protected function getPhpToJsData()
    {
        $client = new ServiceApiClient();

        $currency = $this->getCurrency();

        $currencyFormat = [
            'prefix'             => $currency->getPrefix(),
            'suffix'             => $currency->getSuffix(),
            'decimalDelimiter'   => $currency->getDecimalDelimiter(),
            'thousandsDelimiter' => $currency->getThousandDelimiter(),
            'numDecimals'        => $currency->getE(),
            'rate'               => 1,
        ];

        $filtersApiData = $this->getFiltersApiData();

        if (empty($filtersApiData['conditions'])) {
            $filtersApiData['conditions'] = new \stdClass();
        }

        $data = [
            'filters'               => $this->getFilterConditions(),
            'filtersApi'            => [
                'url'  => $client->getSearchApiUrl(),
                'data' => $filtersApiData,
            ],
            'currencyFormat'        => $currencyFormat,
            'colorFilterNames'      => $this->getColorFilterNames(),
            'colorFilterValues'     => $this->getColorFilterValues(),
            'maxFoldedFilterValues' => static::MAX_FOLDED_FILTER_VALUES,
        ];

        if (!$this->getParam(self::PARAM_IS_ASYNC_FILTERS)) {
            $results = $this->getSearchResults();

            $data += [
                'facets'   => $results['facets'],
                'stats'    => $results['stats'],
                'numFound' => $results['numFoundProducts'],
            ];
        }

        return $data;
    }

    /**
     * Get API request params that frontend code will use to request filters and facets
     *
     * @return array
     */
    protected function getFiltersApiData()
    {
        $client = new ServiceApiClient();

        $apiKey = $client->getApiKey();

        $cnd = clone $this->getParam(self::PARAM_FILTER_CONDITIONS);

        $cnd->{ProductRepo::P_LIMIT}         = [0, 0];
        $cnd->{ProductRepo::P_CLOUD_FILTERS} = [];

        $params = new SearchParameters($cnd);

        return $params->getParameters() + ['apiKey' => $apiKey];
    }

    /**
     * Get current currency
     *
     * @return \XLite\Model\Currency
     */
    protected function getCurrency()
    {
        return XLite::getInstance()->getCurrency();
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
            self::PARAM_FILTER_CONDITIONS => new TypeCollection('Filter conditions', []),
            self::PARAM_IS_ASYNC_FILTERS  => new TypeBool('Filter conditions'),
        ];
    }

    /**
     * Get filter names used to detect Color filter type
     *
     * @return array
     */
    protected function getColorFilterNames()
    {
        return [
            'color',
            'colour',
        ];
    }

    /**
     * Get an associative array that maps color filter values to HTML color values
     *
     * Use to override predefined color values in filters.js
     *
     * @return array
     */
    protected function getColorFilterValues()
    {
        return [];
    }
}
