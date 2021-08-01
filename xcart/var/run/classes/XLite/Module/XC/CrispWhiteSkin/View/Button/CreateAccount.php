<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Button;

/**
 * Register form in popup
 */
class CreateAccount extends \XLite\View\Button\PopupButton
{
    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        $params =  [
            'target' => 'profile',
            'widget' => 'XLite\View\AccountDialog',
            'mode'   => 'register'
        ];

        if (\XLite\Core\Request::getInstance()->fromURL) {
            $params['fromURL'] = \XLite\Core\Request::getInstance()->fromURL;
        }

        return $params;
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Create new account';
    }


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/popup_link.twig';
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return 'popup-button default-popup-button create-account-link';
    }

    /**
     * Default withoutClose value
     *
     * @return boolean
     */
    protected function getDefaultWithoutCloseState()
    {
        return true;
    }
}
