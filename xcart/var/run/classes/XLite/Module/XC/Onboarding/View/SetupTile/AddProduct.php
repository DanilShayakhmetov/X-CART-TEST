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
 * @ListChild(list="onboarding.setup_tiles", weight="10", zone="admin")
 */
class AddProduct extends \XLite\Module\XC\Onboarding\View\SetupTile\ASetupTile
{
    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Add your first product');
    }

    /**
     * @return string
     */
    protected function getContentText()
    {
        return static::t('Get started by adding a product name, picture and price.');
    }

    /**
     * @return string
     */
    protected function getImage()
    {
        return $this->getSVGImage('modules/XC/Onboarding/images/tile-add-product.svg');
    }

    /**
     * @return string
     */
    protected function getButtonLabel()
    {
        return static::t('Add product');
    }

    /**
     * @return string
     */
    protected function getButtonURL()
    {
        return $this->buildURL('onboarding_wizard', 'go_to_step', ['step' => 'add_product_cloud']);
    }

    /**
     * @return string
     */
    protected function getButtonConciergeLinkTitle()
    {
        return 'Concierge: Onboarding Step Add Product Dashboard';
    }

    /**
     * @return string
     */
    protected function getCloseConciergeLinkTitle()
    {
        return 'Concierge: Onboarding Step Add Product Dashboard closed';
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !Config::getInstance()->XC->Onboarding->wizard_force_disabled;
    }

    /**
     * @return bool
     */
    protected function isCompleted()
    {
        return (boolean) WizardState::getInstance()->getLastAddedProductId();
    }

    /**
     * @return string
     */
    protected function getCompletedTileText()
    {
        return static::t('You’ve added your first product. Learn more about import products');
    }
}