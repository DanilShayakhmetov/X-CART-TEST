<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Settings;

class MerchantStatusWarning extends \XLite\View\AView
{
    const PARAM_PAYMENTS_RECEIVABLE     = 'payments_receivable';
    const PARAM_PRIMARY_EMAIL_CONFIRMED = 'primary_email_confirmed';
    const PARAM_OAUTH_THIRD_PARTY       = 'oauth_third_party';

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PAYMENTS_RECEIVABLE     => new \XLite\Model\WidgetParam\TypeBool('Payments receivable', false),
            static::PARAM_PRIMARY_EMAIL_CONFIRMED => new \XLite\Model\WidgetParam\TypeBool('Primary email confirmed', false),
            static::PARAM_OAUTH_THIRD_PARTY       => new \XLite\Model\WidgetParam\TypeBool('oauth third party', false),
        ];
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/settings/PaypalCommercePlatform/merchant_status_warning.twig';
    }
}
