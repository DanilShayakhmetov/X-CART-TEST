<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View;

/**
 * @ListChild (list="layout.header.right", weight="5")
 * @ListChild (list="layout.header.right.mobile", weight="5")
 */
class PaypalBadge extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'main';
        $result[] = 'category';
        $result[] = 'product';

        return $result;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/header/badge.js';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/Paypal';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/header/header.bar.ppbadge.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && \XLite\Module\CDev\Paypal\Main::isHeadIconEnabled();
    }

    /**
     * Get icon url
     *
     * @return string
     */
    public function getIconUrl()
    {
        return \XLite\Core\Layout::getInstance()->getResourceWebPath($this->getDir() . '/header/paypal_accept.svg');
    }
}