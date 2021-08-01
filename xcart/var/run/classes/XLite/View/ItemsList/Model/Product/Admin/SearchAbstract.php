<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Product\Admin;

use XLite\Model\Repo\Product;
use XLite\Model\SearchCondition\RepositoryHandler;
use XLite\View\FormField\AFormField;
use XLite\View\FormField\Select\Category;
use XLite\View\ItemsList\ArrayDataSearchValuesStorage;
use XLite\View\ItemsList\ASearchValuesStorage;
use XLite\View\SearchPanel\ASearchPanel;
use XLite\View\SearchPanel\SimpleSearchPanel;

/**
 * Search product
 *
 *  ListChild (list="admin.center", zone="admin")
 */
abstract class SearchAbstract extends \XLite\View\ItemsList\Model\Product\Admin\AAdmin
{
    /**
     * Widget param names
     */
    const PARAM_SUBSTRING         = 'substring';
    const PARAM_CATEGORY_ID       = 'categoryId';
    const PARAM_SEARCH_IN_SUBCATS = 'searchInSubcats';
    const PARAM_BY_TITLE          = 'by_title';
    const PARAM_BY_DESCR          = 'by_descr';
    const PARAM_BY_SKU            = 'by_sku';
    const PARAM_INVENTORY         = 'inventory';
    const PARAM_ENABLED           = 'enabled';
    const PARAM_INCLUDING         = 'including';

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        $this->sortByModes += [
            static::SORT_BY_MODE_PRICE  => 'Price',
            static::SORT_BY_MODE_NAME   => 'Name',
            static::SORT_BY_MODE_SKU    => 'SKU',
            static::SORT_BY_MODE_AMOUNT => 'Amount',
        ];

        parent::__construct($params);
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return 'XLite\View\SearchPanel\Product\Admin\Main';
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = parent::getFormParams();

        if ('low' === \XLite\Core\Request::getInstance()->{static::PARAM_INVENTORY}) {
            $params[static::PARAM_INVENTORY] = 'low';
        }

        return $params;
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['product_list']);
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
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return static::t('itemslist.admin.product.search.blank');
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget()
    {
        return 'product_list';
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'sku'      => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('SKU'),
                static::COLUMN_NO_WRAP => false,
                static::COLUMN_SORT    => static::SORT_BY_MODE_SKU,
                static::COLUMN_ORDERBY => 100,
            ],
            'name'     => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_MAIN    => true,
                static::COLUMN_NO_WRAP => false,
                static::COLUMN_SORT    => static::SORT_BY_MODE_NAME,
                static::COLUMN_ORDERBY => 200,
                static::COLUMN_LINK    => 'product',
            ],
            'category' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Category'),
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_ORDERBY => 300,
            ],
            'price'    => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Price'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Text\Price',
                static::COLUMN_PARAMS  => ['min' => 0],
                static::COLUMN_SORT    => static::SORT_BY_MODE_PRICE,
                static::COLUMN_ORDERBY => 400,
            ],
            'qty'      => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Stock'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Text\Integer\ProductQuantity',
                static::COLUMN_SORT    => static::SORT_BY_MODE_AMOUNT,
                static::COLUMN_ORDERBY => 500,
            ],
        ];
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return $this->buildURL('product');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add product';
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
        return array_merge(parent::getListNameSuffixes(), ['search']);
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' products-admin-search';
    }

    /**
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\Product\Admin\Search';
    }

    /**
     * Should search params values be saved to session or not
     *
     * @return boolean
     */
    protected function saveSearchConditions()
    {
        return true;
    }

    /**
     * Get search form options
     *
     * @return array
     */
    public function getSearchFormOptions()
    {
        return [
            'target' => 'product_list',
        ];
    }

    /**
     * Get search case (aggregated search conditions) processor
     * This should be passed in here by the controller, but i don't see appropriate way to do so
     *
     * @return \XLite\View\ItemsList\ISearchCaseProvider
     */
    public static function getSearchCaseProcessor()
    {
        return new \XLite\View\ItemsList\SearchCaseProcessor(
            static::getSearchParams(),
            static::getSearchValuesStorage()
        );
    }

    public static function getSearchValuesStorage($forceFallback = false)
    {
        $storage = parent::getSearchValuesStorage($forceFallback);

        if (
            $storage
            && $storage instanceof ASearchValuesStorage
        ) {
            $storage->passFallbackStorage(new ArrayDataSearchValuesStorage([
                static::PARAM_INCLUDING => Product::INCLUDING_ALL
            ]));
        }

        return $storage;
    }

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return array_merge(
            parent::getSearchParams(),
            [
                static::PARAM_SUBSTRING   => [
                    'condition' => new RepositoryHandler('substring'),
                    'widget'    => [
                        ASearchPanel::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                        \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER   => static::t('Search keywords'),
                        AFormField::PARAM_FIELD_ONLY    => true,
                    ],
                ],
                static::PARAM_CATEGORY_ID => [
                    'condition' => new RepositoryHandler('categoryId'),
                    'widget'    => [
                        ASearchPanel::CONDITION_CLASS        => 'XLite\View\FormField\Select\Select2\Category',
                        Category::PARAM_DISPLAY_ANY_CATEGORY => true,
                        Category::PARAM_DISPLAY_NO_CATEGORY  => true,
                        AFormField::PARAM_FIELD_ONLY         => true,
                    ],
                ],
                static::PARAM_INVENTORY   => [
                    'condition' => new RepositoryHandler('inventory'),
                    'widget'    => [
                        ASearchPanel::CONDITION_CLASS => 'XLite\View\FormField\Select\InventoryState',
                        AFormField::PARAM_FIELD_ONLY    => true,
                    ],
                ],
                static::PARAM_INCLUDING   => [
                    'condition' => new RepositoryHandler(
                        Product::P_INCLUDING
                    ),
                    'widget' => [
                        SimpleSearchPanel::CONDITION_TYPE => SimpleSearchPanel::CONDITION_TYPE_HIDDEN,
                        ASearchPanel::CONDITION_CLASS => 'XLite\View\FormField\Select\RadioButtonsList\ProductSearch\IncludingCondition',
                    ],
                ],
                'by_conditions'           => [
                    'condition' => new RepositoryHandler(
                        Product::P_BY
                    ),
                    'widget' => [
                        SimpleSearchPanel::CONDITION_TYPE => SimpleSearchPanel::CONDITION_TYPE_HIDDEN,
                        ASearchPanel::CONDITION_CLASS  => 'XLite\View\FormField\Select\CheckboxList\ProductSearch\ByCondition',
                        AFormField::PARAM_LABEL             => static::t('Search in'),
                    ],
                ],
                static::PARAM_ENABLED     => [
                    'condition' => new \XLite\Model\SearchCondition\Expression\TypeEquality('enabled'),
                    'widget'    => [
                        SimpleSearchPanel::CONDITION_TYPE => SimpleSearchPanel::CONDITION_TYPE_HIDDEN,
                        ASearchPanel::CONDITION_CLASS     => 'XLite\View\FormField\Select\Product\AvailabilityStatus',
                        AFormField::PARAM_LABEL             => static::t('Availability'),
                    ],
                ],
            ]
        );
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        // We initialize structure to define order (field and sort direction) in search query.
        $result->{Product::P_ORDER_BY} = $this->getOrderBy();

        // Prepare filter by 'enabled' field
        $enabledFieldName = Product::P_ENABLED;

        if ($result->{$enabledFieldName} && $result->{$enabledFieldName}->getValue()) {
            $booleanValue = 'enabled' === $result->{$enabledFieldName}->getValue()
                ? true
                : false;

            $result->{$enabledFieldName}->setValue($booleanValue);

        } else {
            unset($result->{$enabledFieldName});
        }

        // Correct filter param 'Search in subcategories'
        if (empty($result->{static::PARAM_CATEGORY_ID})) {
            unset($result->{static::PARAM_CATEGORY_ID});
            unset($result->{static::PARAM_SEARCH_IN_SUBCATS});

        } else {
            $result->{static::PARAM_SEARCH_IN_SUBCATS} = true;
        }

        return $result;
    }

    /**
     * Checks if this itemslist is exportable through 'Export all' button
     *
     * @return boolean
     */
    protected function isExportable()
    {
        return true;
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_NAME;
    }

    // }}}

    // {{{ Content helpers

    /**
     * Get column cell class
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * @return string
     */
    protected function getColumnClass(array $column, \XLite\Model\AEntity $entity = null)
    {
        $class = parent::getColumnClass($column, $entity);

        if ('qty' == $column[static::COLUMN_CODE] && !$entity->getInventoryEnabled()) {
            $class .= ' infinity';
        }

        return $class;
    }

    /**
     * Check - has specified column attention or not
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * @return boolean
     */
    protected function hasColumnAttention(array $column, \XLite\Model\AEntity $entity = null)
    {
        return parent::hasColumnAttention($column, $entity)
            || ('qty' == $column[static::COLUMN_CODE] && $entity && $entity->isLowLimitReached());
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
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
    }

    // }}}

    /**
     * Preprocess category
     *
     * @param mixed                $value  Value
     * @param array                $column Column data
     * @param \XLite\Model\Product $entity Product
     *
     * @return string
     */
    protected function preprocessCategory($value, array $column, \XLite\Model\Product $entity)
    {
        return $value
            ? func_htmlspecialchars($value->getName())
            : '';
    }
}
