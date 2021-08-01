<?php

namespace XLite\Module\Kliken\GoogleAds\View\Header;

/**
 * Header declaration
 *
 * @ListChild (list="head")
 */
class Token extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/Kliken/GoogleAds/verification-token.twig';
    }

    /**
     * Return Google verification token to be put in meta tag
     *
     * @return int
     */
    protected function getGoogleVerificationToken()
    {
        return \XLite\Core\Config::getInstance()->Kliken->GoogleAds->google_verification_token;
    }
}
