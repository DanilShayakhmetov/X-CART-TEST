<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Login form in popup
 */
abstract class PopupLoginAbstract extends \XLite\View\Button\APopupButton
{
    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'button/js/login.js';
        $list[] = 'js/login.js';

        return $list;
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return [
            'target' => 'login',
            'widget' => '\XLite\View\Authorization',
            'fromURL' => $this->getFromURL(),
            'popup'  => '1',
        ];
    }

    /**
     * Return from URL
     *
     * @return string
     */
    protected function getFromURL()
    {
        return \XLite\Core\Request::getInstance()->fromURL
            ?: \XLite::getController()->getURL();
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return trim(parent::getClass() . ' popup-login');
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Login here';
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
