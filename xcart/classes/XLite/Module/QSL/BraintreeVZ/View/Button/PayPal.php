<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\View\Button;

/**
 * PayPal powered by Braintree module's Express checkout button
 *
 * @ListChild (list="cart.panel.totals", weight="100")
 * @ListChild (list="minicart.horizontal.buttons", weight="100")
 * @ListChild (list="add2cart_popup.item.buttons", weight="300")
 */
class PayPal extends \XLite\View\Button\Link
{

    /**
     * Returns true if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $cart = $this->getCart();

        $client = \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance();

        return parent::isVisible()
            && $cart
            && (0 < $cart->getTotal())
            && $cart->checkCart()
            && $client->isConfigured()
            && '1' == $client->getSetting('isPayPal')
            && $client->getPaymentMethod()
            && $client->getPaymentMethod()->isEnabled();
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getBraintreeSkinDir() . 'style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $version = \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::BRAINTREE_JS_VERSION;

        $list[] = [
            'file'      => $this->getBraintreeSkinDir() . 'braintree_payment.js',
            'no_minify' => true,
        ];

        $list[] = $this->getBraintreeSkinDir() . 'button.js';

        $list[] = [
            'url' => 'https://js.braintreegateway.com/web/' . $version . '/js/client.min.js',
        ];

        $list[] = [
            'url' => 'https://js.braintreegateway.com/web/' . $version . '/js/paypal.min.js',
        ];

        $list[] = [
            'url' => 'https://js.braintreegateway.com/web/' . $version . '/js/paypal-checkout.min.js',
        ];

        $list[] = [
            'url' => 'https://www.paypalobjects.com/api/checkout.js',
        ];

        return $list;
    }

    /**
     * Skin directory for checkout
     *
     * @return string
     */
    protected function getBraintreeSkinDir()
    {
        return 'modules/QSL/BraintreeVZ/checkout/';
    }

    /**
     * Returns widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getBraintreeSkinDir() . 'button.twig';
    }

}
