<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\WizardStep;

use Includes\Utils\Module\Manager;

/**
 * ShippingRates
 */
class ShippingRates extends \XLite\Module\XC\Onboarding\View\AWizardStep
{
    /**
     * @return string
     */
    protected function getMoreSettingsLocation()
    {
        return $this->buildURL('shipping_methods');
    }

    /**
     * @return string
     */
    protected function getArticleLink()
    {
        return static::t('https://kb.x-cart.com/shipping/custom_table_rates.html');
    }

    /**
     * @return mixed
     */
    protected function getMethodsData()
    {
        return array_reduce($this->getMethods(), function ($data, $method) {
            /** @var \XLite\Model\Shipping\Method $method */
            $data[$method->getProcessor()] = [
                'method_id' => $method->getMethodId(),
                'processor' => $method->getProcessor(),
                'is_added'  => $method->isAdded(),
                'name'      => $method->getName(),
            ];

            return $data;
        });
    }

    /**
     * Returns online shipping methods (carriers)
     *
     * @return \XLite\Model\Shipping\Method[]
     */
    protected function getMethods()
    {
        return [];
        //$repo = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method');

        //return array_filter($repo->findOnlineCarriers(), function (\XLite\Model\Shipping\Method $method) {
        //    return $method->getAdminIconURL()
        //        && (
        //            !$method->getProcessorModule()
        //            || $method->getProcessorModule()->getEnabled()
        //        );
        //});
    }

    /**
     * @return string
     */
    protected function getDefaultMethodName()
    {
        return static::t('My shipping');
    }

    /**
     * Returns shipping carrier settings url
     *
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return string
     */
    protected function getSettingsURL(\XLite\Model\Shipping\Method $method)
    {
        $url = null;

        $module = $method->getProcessorModule();

        //Manager::getRegistry()->isModuleEnabled($this->getModuleName());
        //
        //if ($module) {
        //    if ($module->isInstalled() && $module->getEnabled()) {
        //        $url = $method->getProcessorObject()
        //            ? $method->getProcessorObject()->getSettingsURL()
        //            : '';
        //
        //    } elseif ($module->isInstalled()) {
        //        $url = $module->getInstalledURL();
        //
        //    } else {
        //        $url = $module->getMarketplaceURL();
        //    }
        //}

        return $url;
    }
}