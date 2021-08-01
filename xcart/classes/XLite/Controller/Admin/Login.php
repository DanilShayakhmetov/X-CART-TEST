<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use Includes\Requirements;
use XLite\Rebuild\Connector;

/**
 * Login
 * FIXME: must be completely refactored
 */
class Login extends \XLite\Controller\Admin\AAdmin
{
    /**
     * getAccessLevel
     *
     * @return integer
     */
    public function getAccessLevel()
    {
        return \XLite\Core\Auth::getInstance()->getCustomerAccessLevel();
    }

    /**
     * Initialization
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        if (empty(\XLite\Core\Request::getInstance()->login)) {
            \XLite\Core\Request::getInstance()->login = \XLite\Core\Auth::getInstance()->remindLogin();
        }
    }

    /**
     * Check - is current place public or not
     *
     * @return boolean
     */
    protected function isPublicZone()
    {
        return true;
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (\XLite\Core\Auth::getInstance()->isAdmin()) {
            $this->setReturnURL($this->buildURL());
        } elseif (\XLite\Core\Request::getInstance()->returnToSpa) {
            \XLite\Core\Session::getInstance()->lastWorkingURL = Connector::getFrontendUrl();
        }
    }

    protected function doActionVerify()
    {
        $authInst = \XLite\Core\Auth::getInstance();
        $authorized = $authInst->isLogged()
            && $authInst->isAdmin()
            && $authInst->hasRootAccess();

        print json_encode([
            'authorized'  => $authorized,
            'admin_login' => $authorized && $authInst->getProfile()
                ? $authInst->getProfile()->getLogin()
                : ''
        ]);

        $this->setSuppressOutput(true);
        $this->silence = true;
    }

    /**
     * Login
     *
     * @return void
     */
    protected function doActionLogin()
    {
        $profile = \XLite\Core\Auth::getInstance()->loginAdministrator(
            \XLite\Core\Request::getInstance()->login,
            \XLite\Core\Request::getInstance()->getNonFilteredData()['password'] ?? null
        );

        if (
            is_int($profile)
            && in_array($profile, array(\XLite\Core\Auth::RESULT_ACCESS_DENIED, \XLite\Core\Auth::RESULT_PASSWORD_NOT_EQUAL, \XLite\Core\Auth::RESULT_LOGIN_IS_LOCKED))
        ) {
            $this->set('valid', false);

            if (in_array($profile, array(\XLite\Core\Auth::RESULT_ACCESS_DENIED, \XLite\Core\Auth::RESULT_PASSWORD_NOT_EQUAL))) {
                \XLite\Core\TopMessage::addError('Invalid login or password');

            } elseif ($profile == \XLite\Core\Auth::RESULT_LOGIN_IS_LOCKED) {
                \XLite\Core\TopMessage::addError('Login is locked out');
            }

            $returnURL = $this->buildURL('login');

        } else {
            if (!\XLite::hasXCNLicenseKey()) {
                \XLite\Core\Session::getInstance()->set(\XLite::SHOW_TRIAL_NOTICE, true);
            }

            $this->checkLoopbackRequest();

            if (isset(\XLite\Core\Session::getInstance()->lastWorkingURL)) {
                $returnURL = \XLite\Core\Session::getInstance()->lastWorkingURL;
                unset(\XLite\Core\Session::getInstance()->lastWorkingURL);

            } else {
                $returnURL = $this->buildURL();
            }

            \Includes\Utils\Session::setAdminCookie();

            \XLite\Core\Database::getEM()->flush();
        }

        $this->setReturnURL($returnURL);
    }

    protected function checkLoopbackRequest()
    {
        $requirementWidget = new \XLite\View\Requirement();
        $requirement = $requirementWidget->getRequirementResult('loopback_request');
        if ($requirement['state'] !== Requirements::STATE_SUCCESS) {
            \XLite\Core\TopMessage::getInstance()->addError('loopback_request.error_message_1', ['url' => \XLite::getXCartURL('https://www.x-cart.com/contact-us.html')]);
        }
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), [
            'logoff',
            'verify'
        ]);
    }

    /**
     * Logoff
     *
     * @return void
     */
    protected function doActionLogoff()
    {
        // TODO: FIX properly
//        \XLite\Controller\Admin\Base\AddonsList::cleanRecentlyInstalledModuleList();

        \Includes\Utils\Session::clearAdminCookie();

        \XLite\Core\Auth::getInstance()->logoff();

        \XLite\Model\Cart::getInstance()->logoff();
        \XLite\Model\Cart::getInstance()->updateOrder();

        \XLite\Core\Database::getEM()->flush();

        $this->setReturnURL($this->buildURL('login'));
    }
}
