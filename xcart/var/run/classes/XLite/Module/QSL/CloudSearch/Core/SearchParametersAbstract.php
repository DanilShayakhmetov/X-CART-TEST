<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite;
use XLite\Core\Auth;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Model\Category;
use XLite\Core\Session;
use XLite\Module\QSL\CloudSearch\Model\Repo\Product;
use XLite\View\ItemsList\Product\Customer\Search as SearchList;


/**
 * Produces CloudSearch search parameters from CommonCell conditions
 */
abstract class SearchParametersAbstract implements SearchParametersInterface
{
    /**
     * @var CommonCell
     */
    protected $cnd;

    /**
     * SearchParameters constructor.
     *
     * @param CommonCell $cnd
     */
    public function __construct(CommonCell $cnd)
    {
        $this->cnd = $cnd;
    }

    /**
     * Get search parameters
     *
     * @return array
     */
    public function getParameters()
    {
        $query           = $this->cnd->{Product::P_SUBSTRING};
        $categoryId      = !XLite::isAdminZone() ? $this->cnd->{Product::P_CATEGORY_ID} : null;
        $searchInSubcats = $this->cnd->{Product::P_SEARCH_IN_SUBCATS};
        $filters         = $this->cnd->{Product::P_CLOUD_FILTERS};
        list($offset, $limit) = $this->cnd->{Product::P_LIMIT};
        list($sortMode, $sortDir) = $this->cnd->{Product::P_ORDER_BY};

        $searchBy = array_keys(array_filter([
            'name'        => $this->cnd->{Product::P_BY_TITLE},
            'description' => $this->cnd->{Product::P_BY_DESCR},
            'sku'         => $this->cnd->{Product::P_BY_SKU},
        ]));

        // Workaround for X-Cart bug when offset is set to a negative value
        $offset = max($offset, 0);

        $membership = Auth::getInstance()->getMembershipId();

        $sortFieldMappings = $this->getSortFieldMappings($categoryId);

        // X-Cart sends asc direction for the below sort modes. Force to use desc.
        if (isset($sortFieldMappings[$sortMode])
            && in_array($sortFieldMappings[$sortMode], ['sort_float_rating', 'sort_int_sales'])
        ) {
            $sortDir = 'desc';
        }

        $sort = isset($sortFieldMappings[$sortMode])
            ? $sortFieldMappings[$sortMode] . ' ' . $sortDir : null;

        // Switch to relevance sorting on default sorting mode if P_SUBSTRING isn't empty
        if (
            $sortMode == SearchList::SORT_BY_MODE_DEFAULT
            && $query !== null
            && $query !== ''
        ) {
            $sort = null;
        }

        $params = [
            'q'               => $query,
            'searchIn'        => $searchBy,
            'categoryId'      => $categoryId,
            'searchInSubcats' => $searchInSubcats,
            'conditions'      => [
                'availability' => $this->getAvailabilityCondition(),
                'categories'   => $this->getCategoriesCondition(),
            ],
            'filters'         => $filters,
            'facet'           => true,
            'membership'      => $membership,
            'sort'            => $sort,
            'offset'          => $offset,
            'limits'          => [
                'products'      => $limit,
                'categories'    => 0,
                'manufacturers' => 0,
                'pages'         => 0,
            ],
            'lang'            => Session::getInstance()->getLanguage()->getCode(),
            'isAdmin'         => XLite::isAdminZone(),
        ];

        $stockStatusCondition = static::getStockStatusCondition($this->cnd->{Product::P_INVENTORY});

        if ($stockStatusCondition) {
            $params['conditions']['stock_status'] = $stockStatusCondition;
        }

        return $params;
    }

    public static function getStockStatusCondition($condition)
    {
        $inStock = [Product::INV_IN, Product::INV_LOW];

        if (!XLite::isAdminZone()) {
            if (!$condition) {
                return [];

            } elseif ($condition === Product::INV_IN) {
                return $inStock;

            } else {
                return [$condition];
            }
        } else {
            if (!$condition || $condition === Product::INV_ALL) {
                return [];

            } elseif ($condition === Product::INV_IN) {
                return $inStock;

            } else {
                return [$condition];
            }
        }
    }

    protected function getAvailabilityCondition()
    {
        if (!XLite::isAdminZone()) {
            return ['Y'];
        } else {
            $availability = $this->cnd->{Product::P_ENABLED};

            if ($availability) {
                return [$availability->getValue() ? 'Y' : 'N'];
            } else {
                return ['Y', 'N'];
            }
        }
    }

    protected function getCategoriesCondition()
    {
        $ids = [];

        if (XLite::isAdminZone() && $this->cnd->{Product::P_CATEGORY_ID}) {
            $ids[] = $this->cnd->{Product::P_CATEGORY_ID};

            if ($this->cnd->{Product::P_SEARCH_IN_SUBCATS}) {
                $subcategories = Database::getRepo('XLite\Model\Category')
                    ->getSubtree($this->cnd->{Product::P_CATEGORY_ID});

                $ids = array_merge($ids, array_map(function (Category $c) {
                    return $c->getCategoryId();
                }, $subcategories));
            }
        }

        return $ids;
    }

    /**
     * Get "X-Cart search mode -> CloudSearch sort field" mapping
     *
     * @param $categoryId
     *
     * @return array
     */
    protected function getSortFieldMappings($categoryId)
    {
        $mapping = [
            'p.arrivalDate'     => 'sort_int_arrival_date',
            'p.price'           => 'sort_float_price',
            'translations.name' => 'sort_str_name',
            'r.rating'          => 'sort_float_rating',
            'p.sales'           => 'sort_int_sales',
            'p.sku'             => 'sort_str_sku',
            'p.amount'          => 'sort_int_amount',
            'vendor.login'      => 'sort_str_vendor',
        ];

        if ($categoryId) {
            $mapping['cp.orderby'] = 'sort_int_orderby_category_' . $categoryId;
        }

        return $mapping;
    }
}
