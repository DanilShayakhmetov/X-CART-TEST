<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Settings;

use XLite\Core\Database;
use XLite\Model\Payment\Method;
use XLite\Module\CDev\Paypal\Core\PaypalCommercePlatform\Onboarding;
use XLite\Module\CDev\Paypal\Main as PaypalMain;

class PaypalCommercePlatformSettings extends \XLite\View\Dialog
{
    /**
     * @var Method
     */
    protected $paymentMethod;

    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'paypal_commerce_platform_settings';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.less';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = $this->getDir() . '/signup.js';

        return $list;
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/Paypal/settings/PaypalCommercePlatform';
    }

    /**
     * @return Method
     */
    protected function getPaymentMethod(): Method
    {
        if (!isset($this->paymentMethod)) {
            $this->paymentMethod = PaypalMain::getPaymentMethod(
                PaypalMain::PP_METHOD_PCP
            );
        }

        return $this->paymentMethod;
    }
}
