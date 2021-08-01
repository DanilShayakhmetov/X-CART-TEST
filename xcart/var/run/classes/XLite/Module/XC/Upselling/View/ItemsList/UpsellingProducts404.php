<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\View\ItemsList;

/**
 * Related products widget for 404 page (customer area)
 *
 * @ListChild (list="404.product", zone="customer", weight="300")
 */
class UpsellingProducts404 extends UpsellingProducts
{
    /**
     * Return target to retrieve this widget from AJAX
     *
     * @return string
     */
    protected static function getWidgetTarget()
    {
        return \XLite::TARGET_404;
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('Alternatives products you may be interested in');
    }

    /**
     * Return default template
     * See setWidgetParams()
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Upselling/404/parts/product/related_products_404_dialog.twig';
    }
}
