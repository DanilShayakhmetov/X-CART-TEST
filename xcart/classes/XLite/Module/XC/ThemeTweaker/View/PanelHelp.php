<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

use XLite\Module\XC\ThemeTweaker\Core\ThemeTweaker;

/**
 * PanelHelp
 *
 * @ListChild (list="layout_settings.settings", zone="admin", weight="50")
 */
class PanelHelp extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/panel/help.twig';
    }

    /**
     * @return string
     */
    protected function getHelpText()
    {
        return static::t('You can customize the look & feel of the store by configuring the layout and adding the custom CSS or HTML code.');
    }

    /**
     * @return string
     */
    public function getStorefrontUrl()
    {
        return \XLite::getInstance()->getShopURL(
            \XLite\Core\Converter::buildURL(
                '',
                '',
                ['activate_mode' => ThemeTweaker::MODE_LAYOUT_EDITOR],
                \XLite::getCustomerScript()),
            \XLite\Core\Config::getInstance()->Security->customer_security
        );
    }

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'modules/XC/ThemeTweaker/panel/style.css'
        ]);
    }
}