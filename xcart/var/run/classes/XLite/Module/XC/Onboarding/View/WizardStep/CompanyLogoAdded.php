<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\WizardStep;

use XLite\Module\XC\Onboarding\Core\WizardState;
use XLite\Module\XC\Onboarding\View\AWizardStep;

/**
 * Add product step
 */
class CompanyLogoAdded extends AWizardStep
{
    const CHECK_NEW_LOGO  = 'checkNewLogo';

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            $this->getDir() . '/step.js',
        ]);
    }

    /**
     * Return Shop URL
     *
     * @return string
     */
    protected function getURLForNewLogoChecking()
    {
        return \XLite::getInstance()->getShopURL(
            '',
            null,
            [self::CHECK_NEW_LOGO => true]
        );
    }

    /**
     * @return string
     */
    protected function lastLogoInclude()
    {
        $protocol    = \XLite\Core\URLManager::isHTTPS() ? 'https' : 'http';
        $hostDetails = \Includes\Utils\ConfigParser::getOptions('host_details');
        $host        = $hostDetails[$protocol . '_host_orig'] ?? null;
        $adminHost   = $hostDetails['admin_host'] ?? null;

        return $adminHost && $adminHost !== $host ? WizardState::COOKIE_LASTLOGO : null;
    }
}