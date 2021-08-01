<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 
namespace XLite\Module\XPay\XPaymentsCloud\View\Tabs;

use XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;

/**
 * X-Payments Saved Cards tab
 */
 class Account extends \XLite\View\Tabs\AccountAbstract implements \XLite\Base\IDecorator
{
    const SUBSCRIPTIONS_TARGET = 'xpayments_subscriptions';

    /**
     * Returns the list of targets where this widget is available
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        if (static::isXpaymentsEnabled()) {
            $list[] = 'xpayments_cards';
            $list[] = static::SUBSCRIPTIONS_TARGET;
        }

        return $list;
    }

    /**
     * Define tabs
     *
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        if (
            $this->getProfile()
            && static::isXpaymentsEnabled()
        ) {
            $tabs['xpayments_cards'] = array(
                 'weight'   => 1200,
                 'title'    => static::t('Saved cards'),
                 'template' => 'modules/XPay/XPaymentsCloud/account/xpayments_cards.twig',
            );

            if ($this->isLogged()) {
                $tabs[static::SUBSCRIPTIONS_TARGET] = [
                    'weight'   => 1500,
                    'title'    => static::t('My Subscriptions'),
                    'template' => 'modules/XPay/XPaymentsCloud/subscriptions.twig',
                ];
            }

        }

        return $tabs;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        if (static::isXpaymentsEnabled()) {
            $list['css'][] = 'modules/XPay/XPaymentsCloud/account/cc_type_sprites.css';
            $list['css'][] = 'modules/XPay/XPaymentsCloud/account/xpayments_cards.less';
        }

        return $list;
    }

    /**
     * Check if X-Payment Cloud payment method is enabled
     *
     * @return bool
     */
    protected static function isXpaymentsEnabled()
    {
        return XPaymentsCloud::getPaymentMethod()
            && XPaymentsCloud::getPaymentMethod()->isEnabled();
    }

}
