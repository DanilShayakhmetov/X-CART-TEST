<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\View\Header;

/**
 * Facebook pixel header
 *
 * @ListChild (list="head", zone="customer")
 */
class Pixel extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/FacebookMarketing/header/pixel.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && \XLite\Module\XC\FacebookMarketing\Main::isPixelEnabled();
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function getPixelId()
    {
        return \XLite\Core\Config::getInstance()->XC->FacebookMarketing->pixel_id;
    }

    /**
     * @return array|bool
     */
    protected function getAdvancedMatchingData()
    {
        $matchingData = [];

        if (\XLite\Module\XC\FacebookMarketing\Main::isAdvancedMatchingEnabled()) {
            $profile = \XLite\Core\Auth::getInstance()->getProfile();

            if (!$profile) {
                $cart = \XLite::getController()->getCart();
                $profile = $cart ? $cart->getProfile() : null;
            }

            if ($profile) {
                if ($profile->getLogin()) {
                    $matchingData['em'] = strtolower($profile->getLogin());
                }

                if ($address = $profile->getBillingAddress()) {
                    if ($address->getFirstname()) {
                        $matchingData['fn'] = mb_strtolower($address->getFirstname());
                    }
                    if ($address->getLastname()) {
                        $matchingData['ln'] = mb_strtolower($address->getLastname());
                    }
                    if ($address->getPhone()) {
                        $matchingData['ph'] = mb_strtolower($address->getPhone());
                    }
                }
            }
        }

        return $matchingData ? json_encode($matchingData) : false;
    }

    /**
     * Check cookies consent
     *
     * @return string
     */
    protected function isConsentRevoked()
    {
        return false;
    }
}