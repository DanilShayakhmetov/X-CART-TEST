<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\SetupTile;

use XLite\Core\TmpVars;

/**
 * @ListChild(list="onboarding.setup_tiles", weight="30", zone="admin")
 */
class PaymentMethods extends \XLite\Module\XC\Onboarding\View\SetupTile\ASetupTile
{
    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Payment Processing');
    }

    /**
     * @return string
     */
    protected function getContentText()
    {
        return static::t('Choose the best way for customers to pay you.');
    }

    /**
     * @return string
     */
    protected function getImage()
    {
        return $this->getSVGImage('modules/XC/Onboarding/images/tile-payments.svg');
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
        return $this->buildURL('payment_settings', '', ['show_add_payment_popup' => 1]);
    }

    /**
     * @return string
     */
    protected function getButtonConciergeLinkTitle()
    {
        return 'Concierge: Onboarding Step Payment Dashboard';
    }

    /**
     * @return string
     */
    protected function getCloseConciergeLinkTitle()
    {
        return 'Concierge: Onboarding Step Payment Dashboard closed';
    }

    /**
     * @return string
     */
    protected function getCompletedTileText()
    {
        return static::t(
            'You can now receive payments from your customers. Add more payment methods',
            [
                'link' => $this->buildURL('payment_settings')
            ]
        );
    }

    /**
     * @return bool
     */
    protected function isCompleted()
    {
        return (boolean) TmpVars::getInstance()->onboarding_payment_changed;
    }
}