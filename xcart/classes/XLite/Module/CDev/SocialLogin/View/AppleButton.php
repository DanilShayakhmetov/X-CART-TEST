<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\View;

/**
 * Facebook sign-in button
 *
 * @ListChild (list="social.login.buttons", zone="customer", weight="20")
 */
class AppleButton extends \XLite\Module\CDev\SocialLogin\View\AButton
{
    /**
     * Widget display name
     */
    const DISPLAY_NAME = 'Apple';

    /**
     * Font awesome class
     */
    const FONT_AWESOME_CLASS = 'fa-apple';

    /**
     * Returns an instance of auth provider
     *
     * @return \XLite\Module\CDev\SocialLogin\Core\AAuthProvider
     */
    protected function getAuthProvider()
    {
        return \XLite\Module\CDev\SocialLogin\Core\AppleAuthProvider::getInstance();
    }

    /**
     * Get widget display name
     *
     * @return string
     */
    public function getName()
    {
        return static::t('Sign in with') . ' ' . static::DISPLAY_NAME;
    }
}
