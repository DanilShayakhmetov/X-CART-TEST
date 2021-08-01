<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Layout\Customer;

/**
 * Desktop navbar part
 */
class DesktopNavbar extends \XLite\View\AView
{
    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/header/navbar.desktop.twig';
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
        return \XLite::getController()->shouldRenderDesktopNavbar();
    }
}
