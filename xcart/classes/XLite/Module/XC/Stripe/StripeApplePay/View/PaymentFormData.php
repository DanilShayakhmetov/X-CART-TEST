<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\StripeApplePay\View;

/**
 * Payment widget
 */
class PaymentFormData extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Stripe/StripeApplePay/payment_form_data.twig';
    }

    /**
     * Get data attributes
     *
     * @return array
     */
    protected function getDataAttributes()
    {//{{{
        $total = $this->getCart()->getCurrency()->roundValue(
            $this->getCart()->getFirstOpenPaymentTransaction()->getValue()
        );

        $selfmethod = $this->getCart()->getPaymentMethod();
        $suffix = $selfmethod->getProcessor()->isTestMode($selfmethod) ? 'Test' : '';
        $description = static::t(
            'X items ($)',
            [
                'count' => $this->getCart()->countQuantity(),
                'total' => $this->formatPrice($total, $this->getCart()->getCurrency()),
            ]
        );

        $parent_method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findOneBy(['service_name' => 'Stripe']);
        $data = [
            'data-key'         => $parent_method->getSetting('publishKey' . $suffix),
            'data-name'        => \XLite\Core\Config::getInstance()->Company->company_name,
            'data-description' => $description,
            'data-total'       => $this->getCart()->getCurrency()->roundValueAsInteger($total),
            'data-currency'    => strtolower($this->getCart()->getCurrency()->getCode()),
            'data-locale'      => $this->getPreparedLanguageCode(),
            'data-country'     => $selfmethod->getProcessor()->getCustomerCountryCode($this->getCart()),
        ];

        if (\XLite\Core\Session::getInstance()->checkoutEmail) {
            $data['data-email'] = \XLite\Core\Session::getInstance()->checkoutEmail;
        } elseif ($this->getCart() && $this->getCart()->getProfile()) {
            $data['data-email'] = $this->getCart()->getProfile()->getEmail();
        }

        return $data;
    }//}}}

    /**
     * @return string
     */
    protected function getPreparedLanguageCode()
    {//{{{
        $code = \XLite\Core\Session::getInstance()->getCurrentLanguage();

        if ($code === 'gb') {
            return 'en';
        }

        return $code ?: 'auto';
    }//}}}
}
