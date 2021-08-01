<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Categories controller
 */
class Categories extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * 'selectorData' target used to get categories for selector on edit product page
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_unique(array_merge(parent::defineFreeFormIdActions(), array('selectorData')));
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->isAJAX() && \XLite\Core\Request::getInstance()->mode === 'removal_notice_popup') {
            return static::t('Products with no assigned categories');
        } elseif ($this->isVisible()) {
            return ($categoryName = $this->getCategoryName())
                ? static::t('Manage category (X)', array('category_name' => $categoryName))
                : static::t('Manage categories');
        } else {
            return static::t('No category defined');
        }
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        if ($this->isVisible() && $this->getCategory()) {
            $this->addLocationNode(
                'Categories',
                $this->buildURL('categories')
            );

            $categories = $this->getCategory()->getPath();
            array_pop($categories);
            foreach ($categories as $category) {
                $this->addLocationNode(
                    $category->getName(),
                    $this->buildURL('categories', '', ['id' => $category->getCategoryId()])
                );
            }
        }
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return !$this->isVisible()
            ? static::t('No category defined')
            : (($categoryName = $this->getCategoryName())
                ? $categoryName
                : static::t('Categories')
            );
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && (
            !$this->getCategoryId() || $this->getCategory()
        );
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->getCategory() ? $this->getCategory()->getName() : '';
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategory()
    {
        if (is_null($this->category)) {
            $this->category = \XLite\Core\Database::getRepo('XLite\Model\Category')
                ->find($this->getCategoryId());
        }

        return $this->category;
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryId()
    {
        return \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Return list of removed categories identifiers from request
     *
     * @return array
     */
    protected function getRemovedCategoriesIdentifiers()
    {
        $result = [];

        $data = \XLite\Core\Request::getInstance()->getData();

        if (!empty($data['delete']) && is_array($data['delete'])) {
            $result = array_keys($data['delete']);
        }

        return $result;
    }

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionUpdateItemsList()
    {
        $removalIdentifiers = $this->getRemovedCategoriesIdentifiers();

        if (!empty($removalIdentifiers)) {
            $this->processRemovalNotice($removalIdentifiers);
        }

        parent::doActionUpdateItemsList();
    }

    /**
     * Process removal notice
     *
     * @param array $ids Category identifiers
     */
    protected function processRemovalNotice($ids)
    {
        if (\XLite\Core\Database::getRepo('XLite\Model\Category')->checkForInternalCategoryProducts($ids)) {
            \XLite\Core\Session::getInstance()->{\XLite\View\ItemsList\Model\Category::IS_DISPLAY_REMOVAL_NOTICE} = true;
        }
    }
}
