<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Authorization
 *
 * @ListChild (list="center", zone="customer")
 */
class Authorization extends \XLite\View\SimpleDialog
{
    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return \XLite\Core\Request::getInstance()->popup
            ? 'authorization/authorization_popup.twig'
            : 'authorization/authorization.twig';
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'login';

        if ('login' == \XLite\Core\Request::getInstance()->mode) {
            $list[] = 'profile';
        }

        return $list;
    }

    /**
     * Get login
     *
     * @return string
     */
    protected function getLogin()
    {
        return \XLite\Core\Request::getInstance()->login ?: null;
    }

    /**
     * Check - login is locked or not
     *
     * @return integer
     */
    protected function isLocked()
    {
        return 0 < $this->getTimeLeftToUnlock();
    }

    /**
     * Return time left to unlock
     *
     * @return integer
     */
    protected function getTimeLeftToUnlock()
    {
        if (!isset($this->timeLeftToUnlock)) {
            $this->timeLeftToUnlock = \XLite\Core\Session::getInstance()->dateOfLockLogin
                ? \XLite\Core\Session::getInstance()->dateOfLockLogin + \XLite\Core\Auth::TIME_OF_LOCK_LOGIN - \XLite\Core\Converter::time()
                : 0;
        }

        return $this->timeLeftToUnlock;
    }

    /**
     * Returns time until unlock in m:s format
     * 
     * @return string
     */
    public function getTimeLeftFormatted()
    {
        $sec = $this->getTimeLeftToUnlock();

        $min = $this->intdivFallback($sec, 60);
        $sec = $sec % 60;

        return sprintf("%02d:%02d", $min, $sec);
    }

    /**
     * intdiv() (PHP 7) function fallback.
     * TODO: Remove when PHP 7 becomes minimal req. (or replace with polyfill)
     * 
     * @param  float $a
     * @param  float $b
     * @return int
     */
    protected function intdivFallback($a, $b)
    {
        return ($a - $a % $b) / $b;
    }

    /**
     * Returns form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\Login\Customer\Main';
    }
}
