<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\SetupTile;

use XLite\Core\TmpVars;
use XLite\Module\XC\Onboarding\Main;

/**
 * @ListChild(list="onboarding.setup_tiles", weight="70", zone="admin")
 */
class DomainName extends \XLite\Module\XC\Onboarding\View\SetupTile\ASetupTile
{
    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Domain name');
    }

    /**
     * @return string
     */
    protected function getContentText()
    {
        return static::t('Your current domain is X', ['domain' => Main::getCloudDomainName()]);
    }

    /**
     * @return string
     */
    protected function getImage()
    {
        return $this->getSVGImage('modules/XC/Onboarding/images/tile-domain.svg');
    }

    /**
     * @return string
     */
    protected function getButtonLabel()
    {
        return static::t('Change');
    }

    /**
     * @return string
     */
    protected function getButtonURL()
    {
        return $this->buildURL('cloud_domain_name');
    }

    /**
     * @return string
     */
    protected function getButtonConciergeLinkTitle()
    {
        return 'Concierge: Onboarding Step Domain Dashboard';
    }

    /**
     * @return string
     */
    protected function getCloseConciergeLinkTitle()
    {
        return 'Concierge: Onboarding Step Domain Dashboard closed';
    }

    /**
     * @return string
     */
    protected function getCompletedTileText()
    {
        return static::t('Your store has its own domain address.');
    }

    /**
     * @return bool
     */
    protected function isCompleted()
    {
        return (boolean) TmpVars::getInstance()->cloud_domain_submit;
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !\XLite::getInstance()->getOptions(['service', 'is_trial']);
    }
}