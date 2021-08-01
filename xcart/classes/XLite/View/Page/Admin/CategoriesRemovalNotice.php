<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Page\Admin;

/**
 * CategoriesRemovalNotice
 */
class CategoriesRemovalNotice extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'page/category/removal_notice_popup.twig';
    }

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'page/category/removal_notice_popup.css';

        return $list;
    }

    /**
     * Return link to products list
     *
     * @return string
     */
    protected function getNoCategoryProductsLink()
    {
        return $this->buildURL('product_list', '', [
            'categoryId' => 'no_category'
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function finalizeTemplateDisplay($template, array $profilerData)
    {
        parent::finalizeTemplateDisplay($template, $profilerData);

        \XLite\Core\Session::getInstance()->{\XLite\View\ItemsList\Model\Category::IS_DISPLAY_REMOVAL_NOTICE} = false;
    }
}