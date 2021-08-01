<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Tabs;

use XLite\Core\Config;

/**
 * Tabs related to paypal settings page
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class PaypalCommercePlatformSettings extends \XLite\View\Tabs\ATabs
{
    /**
     * @var \XLite\Model\Payment\Method
     */
    protected $paymentMethod;

    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'paypal_commerce_platform_settings';

        if (Config::getInstance()->Company->location_country === 'US') {
            $list[] = 'paypal_commerce_platform_credit';
        }

        $list[] = 'paypal_commerce_platform_button';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = [
            'paypal_commerce_platform_settings' => [
                'weight' => 100,
                'title'  => static::t('Settings'),
                'widget' => 'XLite\Module\CDev\Paypal\View\Settings\PaypalCommercePlatformSettings',
            ],
            'paypal_commerce_platform_credit' => [
                'weight' => 200,
                'title'  => static::t('PayPal Credit'),
                'widget' => 'XLite\Module\CDev\Paypal\View\Settings',
            ],
            'paypal_commerce_platform_button' => [
                'weight' => 300,
                'title'  => static::t('Customize the PayPal button'),
                'widget' => 'XLite\Module\CDev\Paypal\View\PaypalButton',
            ],
        ];

        if ('US' !== Config::getInstance()->Company->location_country) {
            unset($tabs['paypal_commerce_platform_credit']);
        }

        return $tabs;
    }

    /**
     * @return \XLite\Model\Payment\Method
     */
    protected function getPaymentMethod()
    {
        if (!isset($this->paymentMethod)) {
            $this->paymentMethod = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
                \XLite\Module\CDev\Paypal\Main::PP_METHOD_PCP
            );
        }

        return $this->paymentMethod;
    }
}
