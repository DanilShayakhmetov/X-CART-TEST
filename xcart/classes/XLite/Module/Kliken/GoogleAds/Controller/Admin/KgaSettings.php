<?php

namespace XLite\Module\Kliken\GoogleAds\Controller\Admin;

use \XLite\Module\Kliken\GoogleAds\Logic\Helper;

class KgaSettings extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('X-Cart Google Ads by Kliken');
    }

    /**
     * Returns module options
     *
     * @return array
     */
    public function getOptions()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Config')
            ->findByCategoryAndVisible($this->getOptionsCategory());
    }

    /**
     * Get options category
     *
     * @return string
     */
    protected function getOptionsCategory()
    {
        return 'Kliken\GoogleAds';
    }

    /**
     * Basically when no action is specified, the page loads normally, but here we want to
     * check if there's some information being sent and try to save it.
     *
     * @return void
     */
    public function doNoAction()
    {
        $accountId = intval(\XLite\Core\Request::getInstance()->maid);
        $appToken  = preg_replace("/[^a-zA-Z0-9]+/", "", \XLite\Core\Request::getInstance()->appt);
        $token     = preg_replace("/[^a-zA-Z0-9]+/", "", \XLite\Core\Request::getInstance()->t);

        // Verify the token
        $sessionToken = \XLite\Core\Session::getInstance()->kliken_signup_token;
        if (empty($sessionToken) || $sessionToken !== $token) return;

        if ($accountId > 0 && !empty($appToken)) {
            $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');

            $repo->createOption([
                'category' => 'Kliken\\GoogleAds',
                'name'     => 'account_id',
                'value'    => $accountId,
            ]);

            $repo->createOption([
                'category' => 'Kliken\\GoogleAds',
                'name'     => 'app_token',
                'value'    => $appToken,
            ]);

            // Remove the session token
            \XLite\Core\Session::getInstance()->kliken_signup_token = null;

            if (Helper::postBackApiKeys(true)) {
                // Redirect back to Kliken
                $this->redirect(Helper::BASE_KLIKEN_URL . '/smb');
            }
        }
    }

    /**
     * Form updated with new info.
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $this->getModelForm()->performAction('update');

        Helper::postBackApiKeys(true);
    }

    /**
     * getModelFormClass
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return '\XLite\View\Model\Settings';
    }

    /**
     * For twig template to determine if we have Kliken account info already or not.
     *
     * @return boolean
     */
    public function hasAccountInfo()
    {
        return Helper::hasAccountInfo();
    }

    public function getKlikenSignUpLink()
    {
        return Helper::buildCreateAccountLink();
    }

    public function getKlikenLink($sub)
    {
        if (substr($sub, 0, 1) !== '/') {
            $sub = '/' . $sub;
        }

        return Helper::BASE_KLIKEN_URL . $sub;
    }
}
