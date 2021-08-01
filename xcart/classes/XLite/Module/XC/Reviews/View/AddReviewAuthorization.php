<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View;

/**
 * Add Review Authorization
 */
class AddReviewAuthorization extends \XLite\View\Authorization
{
    /**
     * Returns form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\XC\Reviews\View\Form\Login\Customer\AddReviewAuthorizationForm';
    }

    /**
     * Returns list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/Reviews/button/popup_login/style.css';

        return $list;
    }
}