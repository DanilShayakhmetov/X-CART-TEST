<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\View\Button;

/**
 * Login form in popup
 */
class PopupLogin extends \XLite\View\Button\PopupLogin implements \XLite\Base\IDecorator
{
    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/SocialLogin/button/js/login.js';

        return $list;
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        $urlParams = parent::prepareURLParams();

        if (\XLite\Core\Request::getInstance()->showLoginPopup) {
            $urlParams['autoloadPopup'] = '1';
        }

        return $urlParams;
    }
}
