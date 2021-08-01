<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XLite\Core\Promo;
use XLite\Module\XC\TaxJar;
use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;

/**
 * Tax banner alert widget
 *
 * @ListChild (list="taxes.top.section", zone="admin", weight="10")
 */
class TaxBannerAlert extends \XLite\View\ModuleBanner
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'tax_classes';
        $result[] = 'vat_tax';
        $result[] = 'canadian_taxes';

        return $result;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'tax_banner_alert/body.twig';
    }

    /**
     * Get module name
     *
     * @return string
     */
    protected function getModuleName()
    {
        return 'XC-AvaTax';
    }

    /**
     * Get logo url
     *
     * @return string
     */
    public function getLogoUrl()
    {
        return \XLite\Core\Layout::getInstance()->getResourceWebPath(
            'tax_banner_alert/avalara_logo.svg'
        );
    }

    public function getModuleLink()
    {
        $moduleName = $this->getModuleName();

        return Manager::getRegistry()->isModuleEnabled($moduleName)
            ? Manager::getRegistry()->getModuleSettingsUrl($moduleName)
            : Manager::getRegistry()->getModuleServiceURL($moduleName);
    }

    /**
     * Returns current target
     *
     * @return string
     */
    protected function getCurrentTarget()
    {
        return \XLite\Core\Request::getInstance()->target;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return \XLite\View\AView::isVisible()
            && \XLite\Controller\Admin\TaxClasses::isEnabled();
    }
}
