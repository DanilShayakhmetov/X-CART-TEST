<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\CloudFilters;

use XLite;
use XLite\Module\QSL\CloudSearch\Main;


/**
 * @ListChild (list="layout.slidebar", zone="customer", weight="20")
 */
class Slidebar extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/CloudSearch/cloud_filters/slidebar.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !$this->isCheckoutLayout();
    }

    /**
     * Checks if widget content should be rendered
     *
     * @return boolean
     */
    protected function shouldRender()
    {
        $controller = XLite::getController();

        $shouldRenderMobileNavbar = !method_exists($controller, 'shouldRenderMobileNavbar')
            || $controller->shouldRenderMobileNavbar();

        return $shouldRenderMobileNavbar
            && $this->isCloudFiltersMobileLinkVisible();
    }
}
