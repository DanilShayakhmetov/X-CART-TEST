<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-2016 Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\ItemsList\Product\Customer;

use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Model\Product;
use XLite\Module\QSL\CloudSearch\Core\ServiceApiClient;
use XLite\Module\QSL\CloudSearch\Core\SearchParameters;
use XLite\Module\QSL\CloudSearch\Main;
use XLite\Module\QSL\CloudSearch\Model\Repo\Product as ProductRepo;
use XLite\Model\WidgetParam\TypeCollection;
use XLite\View\Controller;
use XLite\View\Pager;
use XLite\View\Pager\APager;
use XLite\Module\QSL\CloudSearch\View\ItemsList\Product\ListItem;


trait FilterWithCloudSearchTrait
{
    /**
     * Actual repository search condition to use when displaying filters widget
     *
     * @var CommonCell
     */
    protected $csRepoCondition;

    /**
     * Pre-calculated limit condition to include into search condition to avoid issuing separate
     * CloudSearch requests (one with limit condition and one without)
     *
     * @var array
     */
    protected $csLimitCondition;

    /**
     * Initialize csLimitCondition
     *
     * @return void
     */
    protected function initializeCsLimitCondition()
    {
        /** @var Pager $pager */
        $pager = $this->getWidget(
            [
                APager::PARAM_ITEMS_COUNT => PHP_INT_MAX,
                APager::PARAM_LIST        => $this,
            ],
            $this->getPagerClass()
        );

        $this->csLimitCondition = $pager->getLimitCondition()->{ProductRepo::P_LIMIT};
    }

    /**
     * Return products list
     * May not be called if widget was cached
     *
     * @param CommonCell $cnd       Search condition
     * @param boolean    $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(CommonCell $cnd, $countOnly = false)
    {
        $result = parent::getData($cnd, $countOnly);

        if ($this->csRepoCondition === null) {
            /** @var ProductRepo $repo */
            $repo = Database::getRepo('XLite\Model\Product');

            $this->csRepoCondition = $repo->getCloudSearchConditions();
        }

        return $result;
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = static::PARAM_CLOUD_FILTERS;
    }

    /**
     * Check visibility, initialize and display widget or fetch it from cache.
     *
     * @param string $template Override default template OPTIONAL
     *
     * @return void
     */
    public function display($template = null)
    {
        static $recursionLevel = 0;

        $recursionLevel++;

        parent::display($template);

        if (--$recursionLevel === 0) {
            if ($this->isCacheAllowed()) {
                $cacheKey = array_merge($this->getCacheParameters(), [__CLASS__, __METHOD__]);

                if ($this->csRepoCondition) {
                    $this->getCache()->set($cacheKey, $this->csRepoCondition, $this->getCacheTTL());
                } else {
                    $this->csRepoCondition = $this->getCache()->get($cacheKey);
                }
            }

            $cnd = $this->csRepoCondition;

            if ($cnd !== null && $this->isFilteringWithCloudSearch($cnd)) {
                Controller::showCloudFilters($cnd, $this->isAsynchronouslyFilteringWithCloudSearch($cnd));
            }
        }
    }

    /**
     * Check if header is visible
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return $this->hasResults() || $this->isCloudFiltersMobileLinkVisible();
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
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return $this->getParam(self::PARAM_CLOUD_FILTERS) || parent::isDisplayWithEmptyList();
    }

    /**
     * Get empty list template
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return $this->getParam(self::PARAM_CLOUD_FILTERS)
            ? $this->getEmptyFilteredListTemplate()
            : parent::getEmptyListTemplate();
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
            self::PARAM_CLOUD_FILTERS => new TypeCollection('Cloud filters', []),
        ];
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $list[] = md5(serialize($this->getParam(self::PARAM_CLOUD_FILTERS)));

        return $list;
    }

    /**
     * Return params list to use for search
     *
     * @return CommonCell
     */
    public function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();

        if ($this->isLoadingWithCloudSearch($cnd)) {
            $cnd->{ProductRepo::P_LOAD_PRODUCTS_WITH_CLOUD_SEARCH} = true;

            $cnd->{ProductRepo::P_LIMIT} = $this->csLimitCondition;
        }

        return $cnd;
    }

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return parent::getSearchParams() + [
                ProductRepo::P_CLOUD_FILTERS => self::PARAM_CLOUD_FILTERS,
            ];
    }

    /**
     * Get product list item widget params required for the widget of type getProductWidgetClass().
     *
     * @param Product $product
     *
     * @return array
     */
    protected function getProductWidgetParams(Product $product)
    {
        $params = parent::getProductWidgetParams($product);

        if (defined('XLite\View\Product\ListItem::PARAM_CLOUD_FILTERS_FILTER_VARIANTS')) {
            $cnd = $this->csRepoCondition;

            if (
                $cnd !== null
                && $this->isLoadingWithCloudSearch($cnd)
                && !empty($cnd->{ProductRepo::P_CLOUD_FILTERS})
            ) {
                $client = new ServiceApiClient();

                $results = $client->search(new SearchParameters($cnd));

                if ($results !== null) {
                    foreach ($results['products'] as $p) {
                        if ($p['id'] == $product->getProductId() && !empty($p['variants'])) {
                            $params[ListItem::PARAM_CLOUD_FILTERS_FILTER_VARIANTS] = $p['variants'];

                            break;
                        }
                    }
                }
            }
        }

        return $params;
    }

    /**
     * Returns a list of CSS classes (separated with a space character) to be attached to the items list
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . (!$this->hasResults() ? ' empty-result' : '');
    }

    protected function getCloudFiltersCount()
    {
        $cnd = $this->csRepoCondition;

        if ($cnd !== null
            && $this->isLoadingWithCloudSearch($cnd)
            && !empty($cnd->{ProductRepo::P_CLOUD_FILTERS})
        ) {
            return count($cnd->{ProductRepo::P_CLOUD_FILTERS});
        }

        return 0;
    }
}
