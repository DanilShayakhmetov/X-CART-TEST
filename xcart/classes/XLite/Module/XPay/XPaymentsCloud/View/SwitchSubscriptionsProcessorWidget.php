<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View;

use XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;

/**
 * Class SwitchSubscriptionsProcessorWidget
 * @ListChild (list="admin.center", zone="admin", weight="20")
 * @Decorator\Depend("QSL\XPaymentsSubscriptions")
 */
class SwitchSubscriptionsProcessorWidget extends \XLite\View\AView
{
    /**
     * Return path to widget's resources directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XPay/XPaymentsCloud/switch_subscriptions_processor/';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . 'body.twig';
    }

    /**
     * @inheritDoc
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                $this->getDir() . 'controller.js',
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'x_payments_subscription';
        $list[] = 'module';
        $list[] = 'product';

        return $list;
    }

    /**
     * Get QSL\XPaymentsSubscriptions module ID
     *
     * @return integer
     */
    public function getModuleID()
    {
        return \Includes\Utils\Module\Module::buildId('QSL', 'XPaymentsSubscriptions');
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $request = \XLite\Core\Request::getInstance();

        return parent::isVisible()
            && (
                'product' == $request->target && 'subscription_plan' == $request->page
                || 'module' == $request->target && $this->getModuleID() == $request->moduleId
                || 'x_payments_subscription' == $request->target
            )
            && XPaymentsCloud::isXpaymentsSubscriptionsConfiguredAndActive();
    }

    /**
     * @return bool
     */
    protected function isUseXpaymentsCloudForSubscriptions()
    {
        return XPaymentsCloud::isUseXpaymentsCloudForSubscriptions();
    }

}
