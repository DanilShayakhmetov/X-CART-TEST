<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Database;

/**
 * Select Category controller
 */
class SearchCategories extends \XLite\Controller\Admin\AAdmin
{
    protected function doNoAction()
    {
        $request = \XLite\Core\Request::getInstance();
        $getParams = $request->getGetData();
        $searchText = $getParams['search'] ?? '';
        $page = $getParams['page'];
        $displayNoCategory = $getParams['displayNoCategory'] ?? false;
        $displayRootCategory = $getParams['displayRootCategory'] ?? false;
        $displayAnyCategory = $getParams['displayAnyCategory'] ?? false;
        $excludeCategoryId = $getParams['excludeCategory'] ?? 0;

        $countPerPage = 20;

        $result = [];
        $result['categories'] = [];

        $categoriesFound = Database::getRepo('XLite\Model\Category')
            ->findAllByNamePart($searchText, $page, $countPerPage, $excludeCategoryId);

        foreach ($categoriesFound as $category) {
            $result['categories'][] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'path' => $category->getStringPath(),
                'enabled' => $category->isVisible()
            ];
        }

        if ($displayNoCategory && $page == 1) {
            $notAssigned = [
                'id' => 'no_category',
                'name' => static::t('No category assigned'),
                'path' => static::t('No category assigned'),
            ];

            array_unshift($result['categories'], $notAssigned);
        }
        
        if ($displayRootCategory  && $page == 1) {
            $rootCategory = [
                'id' => Database::getRepo('XLite\Model\Category')->getRootCategoryId(),
                'name' => static::t('Root category'),
                'path' => static::t('Root category'),
            ];

            array_unshift($result['categories'], $rootCategory);
        }

        if ($displayAnyCategory  && $page == 1) {
            $anyCategory = [
                'id' => 0,
                'name' => static::t('Any category'),
                'path' => static::t('Any category'),
            ];

            array_unshift($result['categories'], $anyCategory);
        }

        $result['more'] = ($categoriesFound->count() > $page * $countPerPage) ? true : false;

        $this->printAjax($result);
        die();
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return true;
    }
}
