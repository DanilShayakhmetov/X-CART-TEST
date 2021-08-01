<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\SetupTile;

use XLite\Core\Config;

/**
 * @ListChild(list="onboarding.setup_tiles", weight="20", zone="admin")
 *
 * @Decorator\Depend("CDev\SimpleCMS")
 */
class CompanyLogo extends \XLite\Module\XC\Onboarding\View\SetupTile\ASetupTile
{
    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Upload company logo');
    }

    /**
     * @return string
     */
    protected function getContentText()
    {
        return static::t('Design your online store to fit your brand.');
    }

    /**
     * @return string
     */
    protected function getImage()
    {
        return $this->getSVGImage('modules/XC/Onboarding/images/tile-company-logo.svg');
    }

    /**
     * @return string
     */
    protected function getButtonLabel()
    {
        return static::t('Change logo');
    }

    /**
     * @return string
     */
    protected function getButtonURL()
    {
        return $this->buildURL('onboarding_wizard', 'go_to_step', ['step' => 'company_logo_cloud']);
    }

    /**
     * @return string
     */
    protected function getButtonConciergeLinkTitle()
    {
        return 'Concierge: Onboarding Step Change Logo Dashboard';
    }

    /**
     * @return string
     */
    protected function getCloseConciergeLinkTitle()
    {
        return 'Concierge: Onboarding Step Change Logo Dashboard closed';
    }

    /**
     * @return bool
     */
    protected function isCompleted()
    {
        return (boolean) \XLite\Core\Config::getInstance()->CDev->SimpleCMS->logo;
    }

    /**
     * @return string
     */
    protected function getCompletedTileText()
    {
        return static::t(
            'Your store has a logo! View the storefront',
            [
                'link' => \XLite::getController()->getAccessibleShopURL(),
            ]
        );
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !Config::getInstance()->XC->Onboarding->wizard_force_disabled;
    }
}