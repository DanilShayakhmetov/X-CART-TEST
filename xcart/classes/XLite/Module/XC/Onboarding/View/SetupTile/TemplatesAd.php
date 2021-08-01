<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\SetupTile;

use XLite\Core\Layout;

/**
 * SetupTile (This tile is not used right now, but maybe in the future it will be used)
 */
class TemplatesAd extends \XLite\Module\XC\Onboarding\View\SetupTile\ASetupTile
{
    /**
     * @return string
     */
    protected function getContentText()
    {
        return static::t('100% mobile-friendly eCommerce website templates, fully customizable, affordable, and open source.');
    }

    /**
     * @return string
     */
    protected function getImage()
    {
        $imageUrl = Layout::getInstance()->getResourceWebPath(
            'modules/XC/Onboarding/images/logo-advertise.png',
            Layout::WEB_PATH_OUTPUT_URL
        );

        return '<img src="' . $imageUrl . '">';
    }

    /**
     * @return string
     */
    protected function getButtonLabel()
    {
        return static::t('Templates');
    }

    /**
     * @return string
     */
    protected function getButtonURL()
    {
        return \XLite::getInstance()->getServiceURL('#/templates');
    }

    /**
     * @return string
     */
    protected function getButtonConciergeLinkTitle()
    {
        return 'Concierge: Templates ad Dashboard';
    }

    /**
     * @return string
     */
    protected function getCloseConciergeLinkTitle()
    {
        return 'Concierge: Templates ad Dashboard closed';
    }
}