<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ContactUs\View\FormField\Captcha;

use XLite\View\AView;
use XLite\Module\CDev\ContactUs\Core\ReCaptcha as CoreReCaptcha;

class ReCaptcha extends AView
{
    protected $version;

    public function __construct(array $params = array())
    {
        $this->version = CoreReCaptcha::getInstance()->getVersion();
        parent::__construct($params);
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && CoreReCaptcha::getInstance()->isConfigured();
    }

    /**
     * @inheritDoc
     * @return string
     */
    protected function getDefaultTemplate()
    {
        switch ($this->version) {
            case 2:
            default:
                return 'modules/CDev/ContactUs/contact_us/fields/recaptcha/recaptcha.v2.twig';

            case 3:
                return 'modules/CDev/ContactUs/contact_us/fields/recaptcha/recaptcha.v3.twig';
        }
    }

    /**
     * @return string
     */
    protected function getPublicKey()
    {
        return CoreReCaptcha::getInstance()->getPublicKey();
    }

    /**
     * @return string
     */
    protected function getPrivateKey()
    {
        return CoreReCaptcha::getInstance()->getPrivateKey();
    }
}