<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View;

use XLite\Core\Auth;
use XLite\Core\Config;
use XLite\Core\Request;
use XLite\Model\Role\Permission;
use XLite\Module\XC\Onboarding\Core\WizardState;

/**
 * Wizard mini informer on Dashboard
 *
 * @ListChild(list="admin.center", weight=100, zone="admin")
 */
class MiniWizardStatus extends \XLite\View\AView
{
    /**
     * Add widget specific CSS file
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.less';

        return $list;
    }

    /**
     * Add widget specific JS-file
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/circle-progress.min.js';
        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }


    /**
     * Return widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/Onboarding/mini_wizard_status';
    }

    /**
     * Get block style
     *
     * @return string
     */
    protected function getBlockStyle()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getCurrentProgress()
    {
        return WizardState::getInstance()->getWizardProgress();
    }

    /**
     * @return string
     */
    protected function getButtonLabel()
    {
        return 'Continue';
    }

    /**
     * @return string
     */
    protected function getWizardUrl()
    {
        return $this->buildURL('onboarding_wizard');
    }

    /**
     * @return bool
     */
    protected function checkACL()
    {
        return Auth::getInstance()->isPermissionAllowed(Permission::ROOT_ACCESS);
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->isTargetIsAllowed()
            && Config::getInstance()->XC->Onboarding->wizard_state !== 'disabled'
            && $this->getCurrentProgress() < 100;
    }

    /**
     * @return string
     */
    protected function isTargetIsAllowed()
    {
        $checks = [
            'product'          => \XLite::getController()->getTarget() === 'product',
            'product_list'     => \XLite::getController()->getTarget() === 'product_list',
            'order_list'       => \XLite::getController()->getTarget() === 'order_list',
            'store_info'       =>
                \XLite::getController()->getTarget() === 'settings'
                && Request::getInstance()->page === 'Company',
            'general'          => \XLite::getController()->getTarget() === 'general_settings',
            'payment_settings' => \XLite::getController()->getTarget() === 'payment_settings',

            'countries'        => \XLite::getController()->getTarget() === 'countries',
            'shipping_methods' => \XLite::getController()->getTarget() === 'shipping_methods',
            'tax_classes'      => \XLite::getController()->getTarget() === 'tax_classes',
            'localization'     => \XLite::getController()->getTarget() === 'units_formats',
            'translations'     => \XLite::getController()->getTarget() === 'languages',
            'notifications'    => \XLite::getController()->getTarget() === 'notifications',
            'seo'              =>
                \XLite::getController()->getTarget() === 'settings'
                && Request::getInstance()->page === 'CleanURL',
        ];

        foreach ($checks as $check) {
            if ($check) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getStatusMessage()
    {
        return $this->getCurrentProgress() > 0
            ? static::t('X% Wizard completion', ['X' => $this->getCurrentProgress()])
            : static::t('Let’s set up your store');
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }
}