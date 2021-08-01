<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Form;

/**
 * Paypal settings form
 */
class PaypalCreditSettings extends \XLite\Module\CDev\Paypal\View\Form\Settings
{
    /**
     * Get default target field value
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        if (\XLite::getController() instanceof \XLite\Module\CDev\Paypal\Controller\Admin\PaypalCommercePlatformCredit) {
            return 'paypal_commerce_platform_credit';
        }

        return 'paypal_credit';
    }
}