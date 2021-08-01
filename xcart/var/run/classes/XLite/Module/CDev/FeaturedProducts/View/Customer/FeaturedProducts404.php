<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\View\Customer;

/**
 * Featured products widget for 404 page
 *
 * @ListChild (list="404.category", zone="customer", weight="300")
 */
class FeaturedProducts404 extends FeaturedProducts
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = \XLite::TARGET_404;

        return $result;
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('Related products you may be interested in');
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return $this->getCategory() && $this->hasResults();
    }

    /**
     * Return default template
     * See setWidgetParams()
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/FeaturedProducts/404/parts/category/featured_products_404_dialog.twig';
    }
}
