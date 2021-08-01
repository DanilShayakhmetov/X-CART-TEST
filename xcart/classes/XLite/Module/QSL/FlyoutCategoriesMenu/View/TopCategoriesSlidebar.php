<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\FlyoutCategoriesMenu\View;

/**
 * Sidebar categories list
 */
abstract class TopCategoriesSlidebar extends \XLite\View\TopCategoriesSlidebar implements \XLite\Base\IDecorator
{
    /**
     * Preprocess DTO
     *
     * @param  array $categoryDTO
     *
     * @return array
     */
    protected function preprocessDTO($categoryDTO)
    {
        $categoryDTO = parent::preprocessDTO($categoryDTO);

        if ($this->isShowCatIcon()) {
            $categoryDTO['image'] = $categoryDTO['image_id']
                ? \XLite\Core\Database::getRepo('XLite\Model\Image\Category\Image')->find($categoryDTO['image_id'])
                : null;
        }

        return $categoryDTO;
    }

    /**
     * @param array $categories
     *
     * @return array
     */
    protected function postprocessDTOs($categories)
    {
        $categories = parent::postprocessDTOs($categories);

        if (\XLite\Core\Config::getInstance()->QSL->FlyoutCategoriesMenu->fcm_show_product_num) {
            foreach ($categories as $categoryDTO) {
                $tmpParent = isset($categories[$categoryDTO['parent_id']])
                    ? $categories[$categoryDTO['parent_id']]
                    : null;

                $productsCount = $categoryDTO['productsCount'];
                while ($tmpParent) {
                    $categories[$tmpParent['id']]['productsCount'] += $productsCount;
                    $tmpParent = isset($categories[$tmpParent['parent_id']])
                        ? $categories[$tmpParent['parent_id']]
                        : null;
                }
            }
        }

        return $categories;
    }

    /**
     * Check if word wrap disabled
     *
     * @return boolean
     */
    protected function isShowCatIcon()
    {
        return \XLite\Core\Config::getInstance()->QSL->FlyoutCategoriesMenu->fcm_show_icons;
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'categories/tree';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'categories/tree/body.twig';
    }
}
