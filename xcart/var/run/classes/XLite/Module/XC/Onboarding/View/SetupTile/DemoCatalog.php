<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\SetupTile;

use XLite\Core\Config;
use XLite\Module\XC\Onboarding\Core\WizardState;

/**
 * @ListChild(list="onboarding.setup_tiles", weight="60", zone="admin")
 */
class DemoCatalog extends \XLite\Module\XC\Onboarding\View\SetupTile\ASetupTile
{
    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Demo Catalog');
    }

    /**
     * @return string
     */
    protected function getContentText()
    {
        return static::t('We have used a sample catalog to demonstrate X-Cart features. If you don\'t need it anymore you can delete all sample data.');
    }

    /**
     * @return string
     */
    protected function getImage()
    {
        return $this->getSVGImage('modules/XC/Onboarding/images/tile-demo-catalog.svg');
    }

    /**
     * @return string
     */
    protected function getButtonLabel()
    {
        return static::t('Delete demo Catalog');
    }

    /**
     * @return string
     */
    protected function getButtonURL()
    {
        return $this->buildURL('onboarding_wizard', 'remove_demo_catalog', ['returnURL' => $this->buildURL('')]);
    }

    /**
     * @return string
     */
    protected function getButtonConciergeLinkTitle()
    {
        return 'Concierge: Onboarding Step Delete demo Catalog Dashboard';
    }

    /**
     * @return string
     */
    protected function getCloseConciergeLinkTitle()
    {
        return 'Concierge: Onboarding Step Delete demo Catalog Dashboard closed';
    }

    /**
     * @return string
     */
    protected function getCompletedTileText()
    {
        return static::t('The demo catalog has been deleted.');
    }

    /**
     * @return bool
     */
    protected function isCompleted()
    {
        return !WizardState::getInstance()->hasDemoCatalog();
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