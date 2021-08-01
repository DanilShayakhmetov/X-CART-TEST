<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View;

use XLite\Core\PreloadedLabels\ProviderInterface;
use XLite\Core\Session;
use \XLite\Module\XC\FastLaneCheckout;

/**
 * Checkout fastlane app container
 */
class CheckoutApp extends \XLite\View\AView implements ProviderInterface
{
    public function getJSFiles()
    {
        return [
            FastLaneCheckout\Main::getSkinDir() . 'checkout_fastlane/component.js',
            FastLaneCheckout\Main::getSkinDir() . 'checkout_fastlane/store_modules/sections.js',
            FastLaneCheckout\Main::getSkinDir() . 'checkout_fastlane/store_modules/order.js',
            FastLaneCheckout\Main::getSkinDir() . 'checkout_fastlane/store.js',
            FastLaneCheckout\Main::getSkinDir() . 'checkout_fastlane/actions.js',
        ];
    }

    public function getCSSFiles()
    {
        return [
            [
                'file' => FastLaneCheckout\Main::getSkinDir() . 'checkout_fastlane/style.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            ],
            [
                'file'  => 'checkout/css/animations.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            ]
        ];
    }

    public function getCommonFiles()
    {
        return [
            static::RESOURCE_JS => static::getVueLibraries(),
        ];
    }

    /**
     * @return string
     */
    protected function getCheckoutUID()
    {
        /** @var \XLite\Model\Cart $cart */
        $cart = \XLite::getController()->getCart();

        return md5(serialize([
            $cart->getItemsAmountKeyFingerprint(),
            Session::getInstance()->getID()
        ]));
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'checkout_fastlane/app.twig';
    }

    /**
     * Defines the store name
     *
     * @return string
     */
    protected function getStoreName()
    {
        return \XLite\Core\Config::getInstance()->Company->company_name;
    }

    /**
     * Defines the secondary checkout title
     *
     * @return string
     */
    protected function getSecondaryTitle()
    {
        return '';
    }

    /**
     * Defines the navigation title
     *
     * @return string
     */
    protected function getNavigationTitle()
    {
        return '';
    }

    /**
     * Get preloaded labels
     *
     * @return array
     */
    public function getPreloadedLanguageLabels()
    {
        $list = [
            'Menu',
            'Edit email',
            'Edit address',
            'Enter a correct email',
            'Order can not be placed because not all required fields are completed. Please check the form and try again.',
            'Field is required!',
            'Place order',
            'same as shipping',
            'Click to finish your order',
            'Order cannot be placed because some steps are not completed',
            'Click to proceed to the next step',
            'Some of the required fields were not completed. Please check the form and try again',
            'Next step',
            'Shipping to',
            'Billing to',
            'Transaction is in progress'
        ];

        $data = [];
        foreach ($list as $name) {
            $data[$name] = static::t($name);
        }

        return $data;
    }
}
