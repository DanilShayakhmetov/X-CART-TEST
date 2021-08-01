<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\View\ItemsList\Model;

use XLite\Core\Database;
use XLite\Core\TopMessage;
use XLite\Model\AEntity;
use XLite\Module\XC\ProductTags\Model\TagTranslation;

/**
 * Tag items list
 */
class Tag extends \XLite\View\ItemsList\Model\Table
{
    protected $duplicatesCount = 0;

    const PARAM_SEARCH_NAME = 'translations.name';

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name'     => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_CLASS   => 'XLite\Module\XC\ProductTags\View\FormField\Inline\Input\Text',
                static::COLUMN_MAIN    => true,
                static::COLUMN_PARAMS  => [
                    \XLite\View\FormField\Input\Base\StringInput::PARAM_REQUIRED => true,
                    \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 128,
                ],
                static::COLUMN_ORDERBY => 100,
            ],
            'products' => [
                static::COLUMN_NAME    => static::t('Products'),
                static::COLUMN_ORDERBY => 200,
                static::COLUMN_LINK    => 'product_list',
            ],
        ];
    }

    /**
     * Return search parameters.
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return [
            \XLite\Module\XC\ProductTags\Model\Repo\Tag::SEARCH_NAME => static::PARAM_SEARCH_NAME,
        ];
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge($this->requestParams, static::getSearchParams());
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if (is_string($paramValue)) {
                $paramValue = trim($paramValue);
            }

            if ('' !== $paramValue) {
                $result->$modelParam = $paramValue;
            }
        }

        $result->{\XLite\Module\XC\ProductTags\Model\Repo\Tag::P_ORDER_BY} = $this->getOrderBy();

        return $result;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\XC\ProductTags\Model\Tag';
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
        if ('products' == $column[static::COLUMN_CODE]) {
            $result = \XLite\Core\Converter::buildURL(
                'product_list',
                '',
                ['action' => 'search', 'substring' => $entity->getName(), 'by_tag' => 'Y']
            );
        } else {
            $result = parent::buildEntityURL($entity, $column);
        }

        return $result;
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildURL('tag');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New tag';
    }

    /**
     * @param \XLite\Module\XC\ProductTags\Model\Tag|AEntity $entity
     *
     * @return bool
     */
    protected function prevalidateNewEntity(AEntity $entity)
    {
        /* @var TagTranslation $translation */
        $translation = $entity->getTranslation();
        $repo = Database::getRepo('XLite\Module\XC\ProductTags\Model\TagTranslation');

        if ($repo->findOneBy([
            'name' => $translation->getName(),
            'code' => $translation->getCode(),
        ])) {
            $this->duplicatesCount++;
            return false;
        }

        return parent::prevalidateNewEntity($entity);
    }

    protected function processCreateErrors()
    {
        if ($this->duplicatesCount) {
            TopMessage::addWarning('{{count}} Tag duplicates ignored', [
                'count' => $this->duplicatesCount
            ]);
        }
        parent::processCreateErrors();
    }

    // {{{ Column processing

    /**
     * Get column value for 'products' column
     *
     * @param \XLite\Module\XC\ProductTags\Model\Tag $entity tag
     *
     * @return string
     */
    protected function getProductsColumnValue(\XLite\Module\XC\ProductTags\Model\Tag $entity)
    {
        return $entity->getProducts()->count();
    }

    // }}}

    // {{{ Behaviors

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
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
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    // }}}

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return static::t('itemslist.admin.tag.blank');
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' tags';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\XC\ProductTags\View\StickyPanel\ItemsList\Tag';
    }
}
