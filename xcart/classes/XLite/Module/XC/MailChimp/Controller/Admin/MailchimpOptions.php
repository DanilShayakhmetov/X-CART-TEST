<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Controller\Admin;

use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;
use \XLite\Module\XC\MailChimp\Core;

/**
 * Shopgate connector module settings
 */
class MailchimpOptions extends \XLite\Controller\Admin\Module
{
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            [
                'authenticate'
            ]
        );
    }

    /**
     * Get current module ID
     *
     * @return void
     */
    public function handleRequest()
    {
        parent::handleRequest();

        $sections = Core\MailChimpSettings::getInstance()->getAllSections();

        if (!in_array(\XLite\Core\Request::getInstance()->section, $sections)) {

            $this->setHardRedirect();

            $this->setReturnURL(
                $this->buildURL(
                    'mailchimp_options',
                    '',
                    array(
                        'section'  => $this->getCurrentSection(),
                    )
                )
            );

            $this->doRedirect();
        }
    }

    /**
     *
     */
    protected function doActionAuthenticate()
    {
        $redirectURL   = $this->getShopURL(
            $this->buildURL(
                'mailchimp_options',
                'endAuth'
            )
        );

        $oauthCore = $this->createClient();
        $auth_url = $oauthCore->getAuthUrl($redirectURL);

        if ($auth_url) {
            $this->redirect($auth_url);
        }
    }

    /**
     *
     */
    protected function doActionSetApiKey()
    {
        if (\XLite\Core\Request::getInstance()->mailchimp_key) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'XC\MailChimp',
                'name'     => 'mailChimpAPIKey',
                'value'    => \XLite\Core\Request::getInstance()->mailchimp_key,
            ]);
        }
    }


    /**
     *
     */
    protected function doActionEndAuth()
    {
        $redirectURL = $this->getShopURL(
            $this->buildURL(
                'mailchimp_options',
                'endAuth'
            )
        );

        $oauthCore = $this->createClient();
        $token = $oauthCore->getToken(\XLite\Core\Request::getInstance()->code, $redirectURL);

        if ($token) {
            \XLite\Core\TopMessage::addInfo('Successfully authenticated');
            $this->saveAsApiKey($token);
        } else {
            \XLite\Core\TopMessage::addError('Cannot authenticate');
        }

        $this->setReturnURL($this->buildURL(
            'mailchimp_options',
            '',
            [ 'section' => 'settings' ]
        ));
    }

    /**
     * @param $token
     */
    protected function saveAsApiKey($token)
    {
        $oauthCore = $this->createClient();
        try {
            $metadata = $oauthCore->getTokenMetadata($token);
            $dc = $metadata->dc;

            $key = $token . '-' . $dc;

            \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption(
                array(
                    'category' => 'XC\MailChimp',
                    'name'     => 'mailChimpAPIKey',
                    'value'    => $key,
                )
            );
            \XLite\Core\Database::getEM()->flush();

        } catch (\Exception $e) {
            \XLite\Core\TopMessage::addError('Cannot authenticate');
        }
    }

    /**
     * @return Core\OAuth
     */
    protected function createClient()
    {
        $clientId     = '371104556554';
        $clientSecret = '39bbf84b9cbae6799294a581f95cad619a926cea538e64a07d';
        $oathProxyUrl = 'https://mc-end-auth.qtmsoft.com/oauth.php';

        return new Core\OAuth($clientId, $clientSecret, $oathProxyUrl);
    }

    /**
     * Get current module ID
     *
     * @return string
     */
    protected function getModuleID()
    {
        return Module::buildId('XC', 'MailChimp');
    }

    /**
     * Class name for the \XLite\View\Model\ form (optional)
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return '\XLite\Module\XC\MailChimp\View\Model\ModuleSettings';
    }

    /**
     * Get current section
     *
     * @return string
     */
    protected function getCurrentSection()
    {
        $return = \XLite\Core\Request::getInstance()->section;

        if (!in_array($return, Core\MailChimpSettings::getInstance()->getAllSections())) {
            $return = Core\MailChimpSettings::SECTION_MAILCHIMP_API;
        }

        return $return;
    }
}
