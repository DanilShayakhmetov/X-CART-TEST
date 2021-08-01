<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\SetupTile;

use XLite\Core\TmpVars;

/**
 * @ListChild(list="onboarding.setup_tiles", weight="40", zone="admin")
 */
class ShippingMethods extends \XLite\Module\XC\Onboarding\View\SetupTile\ASetupTile
{
    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Shipping');
    }

    /**
     * @return string
     */
    protected function getContentText()
    {
        return static::t('Onboarding: Get shipping rates from major shipping carrier companies.');
    }

    /**
     * @return string
     */
    protected function getImage()
    {
        return $this->getSVGImage('modules/XC/Onboarding/images/tile-shipping.svg');
    }

    /**
     * @return string
     */
    protected function getButtonLabel()
    {
        return static::t('Set it up');
    }

    /**
     * @return string
     */
    protected function getButtonURL()
    {
        return $this->buildURL('shipping_methods', '', ['show_add_shipping_popup' => 1]);
    }

    /**
     * @return string
     */
    protected function getButtonConciergeLinkTitle()
    {
        return 'Concierge: Onboarding Step Shipping Dashboard';
    }

    /**
     * @return string
     */
    protected function getCloseConciergeLinkTitle()
    {
        return 'Concierge: Onboarding Step Shipping Dashboard closed';
    }

    /**
     * @return string
     */
    protected function getCompletedTileText()
    {
        return static::t(
            'Delivery methods are configured. Add another shipping method',
            [
                'link' => $this->buildURL('shipping_methods')
            ]
        );
    }

    /**
     * @return bool
     */
    protected function isCompleted()
    {
        return (boolean) TmpVars::getInstance()->onboarding_shipping_changed;
    }
}