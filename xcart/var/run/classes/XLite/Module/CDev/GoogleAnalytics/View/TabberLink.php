<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View;

use Includes\Utils\Module\Manager;

/**
 * Tabber link widget
 *
 * @ListChild (list="tabs.content", zone="admin")
 */
class TabberLink extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['orders_stats']);
    }

    /**
     * Check if the Google Analitics module is configured
     *
     * @return boolean
     */
    protected function isConfigured()
    {
        return (bool) \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->ga_account;
    }

    /**
     * Defines the module link to configure
     *
     * @return string
     */
    protected function getModuleLink()
    {
        return Manager::getRegistry()->getModuleSettingsUrl('CDev', 'GoogleAnalytics');
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoogleAnalytics/tabs/link.twig';
    }
}
