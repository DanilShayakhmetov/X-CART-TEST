<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use Includes\Utils\Module\Manager;

/**
 * Automate shipping routine page view
 */
class AutomateShippingReturns extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            array(
                'automate_shipping_refunds'
            )
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'automate_shipping_returns/body.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

         $list[] = 'automate_shipping_returns/style.css';

        return $list;
    }

    /**
     * Is 71lbs installed
     *
     * @return boolean
     */
    public function isModuleInstalled()
    {
        return (bool) Manager::getRegistry()->getModule('AutomatedShippingRefunds71LBS\SeventyOnePounds');
    }

    /**
     * Get "Configure" link
     *
     * @return string
     */
    public function getConfigureLink()
    {
        return Manager::getRegistry()->isModuleEnabled('AutomatedShippingRefunds71LBS\SeventyOnePounds')
            ? Manager::getRegistry()->getModuleSettingsUrl('AutomatedShippingRefunds71LBS\SeventyOnePounds')
            : Manager::getRegistry()->getModuleServiceURL('AutomatedShippingRefunds71LBS\SeventyOnePounds');
    }

    /**
     * Get "enable module" link
     *
     * @return string
     */
    public function getEnableLink()
    {
        return Manager::getRegistry()->getModuleServiceURL('AutomatedShippingRefunds71LBS\SeventyOnePounds');
    }
}
