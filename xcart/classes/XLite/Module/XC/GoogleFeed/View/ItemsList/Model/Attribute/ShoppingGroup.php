<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\View\ItemsList\Model\Attribute;

use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Model\Attribute;
use XLite\Module\XC\GoogleFeed\Model\SearchCondition\Expression\TypeSearchGroup;
use XLite\View\ItemsList\SearchCaseProcessor;

/**
 * Search product
 */
class ShoppingGroup extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget param names
     */
    const PARAM_SUBSTRING         = 'substring';
    const PARAM_GROUP             = 'group';

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return '\XLite\View\SearchPanel\SimpleSearchPanel';
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Attribute';
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return [
            'google_shopping_groups'
        ];
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
        return 'google_shopping_groups';
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
            [
                'groupToSet' => '',
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
        return [
            'name' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_MAIN    => true,
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_ORDERBY => 100,
            ],
            'type' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Attribute type'),
                static::COLUMN_ORDERBY => 200,
                static::COLUMN_LINK    => 'type',
            ],
            'qty' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Product count'),
                static::COLUMN_ORDERBY => 300,
            ],
            'googleShoppingGroup' => [
                static::COLUMN_NAME     => static::t('Google shopping group'),
                static::COLUMN_CLASS    => 'XLite\Module\XC\GoogleFeed\View\FormField\Inline\Select\ShoppingGroup',
                static::COLUMN_ORDERBY  => 400,
            ],
        ];
    }

    /**
     * @param Attribute $entity
     * @return string
     */
    protected function getTypeColumnValue(Attribute $entity)
    {
        if ($entity->getProduct()) {
            return static::t('Product-specific') . ' (' . $entity->getProduct()->getName() . ')';
        }

        if ($entity->getProductClass()) {
            return static::t('Class X', ['class' => $entity->getProductClass()->getName()]);
        }

        switch ($entity->getType()) {
            case Attribute::TYPE_HIDDEN:
                return static::t('Global Hidden field');
            case Attribute::TYPE_CHECKBOX:
                return static::t('Global Yes/No');
            case Attribute::TYPE_SELECT:
                return static::t('Global Plain field');
            case Attribute::TYPE_TEXT:
                return static::t('Global Textarea');
            default:
                return static::t('Global');
        }
    }

    /**
     * @param Attribute $entity
     * @return string
     */
    protected function getQtyColumnValue(Attribute $entity)
    {
        if ($entity->getProduct()) {
            return (string) 1;
        }

        if ($entity->getProductClass()) {
            return (string) $entity->getProductClass()->getProductsCount();
        }

        return Database::getRepo('XLite\Model\Attribute')->countProductsWithValues($entity);
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

            case 'type':
                if ($entity->getProduct()) {
                    return Converter::buildURL(
                        'product',
                        '',
                        ['product_id' => $entity->getProduct()->getProductId(), 'page' => 'attributes']
                    );
                }

                if ($entity->getProductClass()) {
                    return Converter::buildURL(
                        'attributes',
                        '',
                        ['product_class_id' => $entity->getProductClass()->getId()]
                    );
                }

                return Converter::buildURL(
                    'attributes',
                    ''
                );

            default:
                return parent::buildEntityURL($entity, $column);
        }
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return array_merge(parent::getListNameSuffixes(), array('shopping_group'));
    }

    /**
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\XC\GoogleFeed\View\StickyPanel\ShoppingGroup';
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
        return array(
            'target' => 'google_shopping_groups'
        );
    }

    /**
     * Get search case (aggregated search conditions) processor
     * This should be passed in here by the controller, but i don't see appropriate way to do so
     *
     * @return \XLite\View\ItemsList\ISearchCaseProvider
     */
    public static function getSearchCaseProcessor()
    {
        return new SearchCaseProcessor(
            static::getSearchParams(),
            static::getSearchValuesStorage()
        );
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
            array(
                static::PARAM_SUBSTRING    => array(
                    'condition'     => new \XLite\Model\SearchCondition\Expression\TypeLike('translations.name'),
                    'widget'            => array(
                        \XLite\View\SearchPanel\ASearchPanel::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                        \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Search attribute name'),
                        \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
                    ),
                ),
                static::PARAM_GROUP    => array(
                    'condition'     => new TypeSearchGroup('googleShoppingGroup'),
                    'widget'            => array(
                        'nonSelected'   => static::t('Any google group'),
                        'emptySelected' => true,
                        \XLite\View\SearchPanel\ASearchPanel::CONDITION_CLASS => 'XLite\Module\XC\GoogleFeed\View\FormField\Select\ShoppingGroup',
                        \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
                    ),
                ),
            )
        );
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();
    }

    // {{{ Search

    // }}}

    // {{{ Content helpers

    // }}}

    // {{{ Behaviors

    protected function isSelectable()
    {
        return true;
    }

    // }}}
}
