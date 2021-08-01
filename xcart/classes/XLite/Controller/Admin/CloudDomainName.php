<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Mail\Common\ChangeCloudDomain;
use XLite\Logic\WhoisService;

/**
 * Domain name page controller
 */
class CloudDomainName extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            ['transfered']
        );
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Domain name');
    }

    /**
     * @return bool
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && \XLite::getInstance()->getOptions(['service', 'is_cloud'])
            && !\XLite::getInstance()->getOptions(['service', 'is_trial']);
    }

    protected function doActionTransfered()
    {
        $result = \XLite\Core\Request::getInstance()->result;

        if ($result === 'success') {
            $domainName           = \XLite\Core\Request::getInstance()->domain;
            $whoisxmlapi          = WhoisService::create($domainName);
            $nameServers          = $whoisxmlapi->getNameServers();
            $incorrectNameServers = array_diff($nameServers, $this->getSampleNsServers());

            if ($incorrectNameServers) {
                $registrarWhoisServer = $whoisxmlapi->getRegistrarWhoisServer();
                $registrarsHelpUrls   = $this->getRegistrarsHelpUrls();
                $incorrectNsWarning   = static::t('The nameservers for your domain may have been entered incorrectly.');

                if (isset($registrarsHelpUrls[$registrarWhoisServer])) {
                    $incorrectNsWarning .= static::t(
                        'You can check your nameservers settings by following the instructions here: {{helpUrl}}.',
                        ['helpUrl' => $registrarsHelpUrls[$registrarWhoisServer]]
                    );
                } else {
                    $incorrectNsWarning .= static::t('Please check the nameservers settings in your account on the registrar\'s website.');
                }

                $incorrectNsWarning .= static::t('If you need help with the nameservers settings just drop us a line to helpdesk@x-cart.com, we\'ll help you fix it.');

                \XLite\Core\TopMessage::addWarning($incorrectNsWarning);
            } else {
                \XLite\Core\TopMessage::addInfo('Your domain name has been changed. It may take a few days for your domain name provider to make all the necessary changes and a few more days for the changes to propagate throughout the internet.');
                \XLite\Core\TmpVars::getInstance()->cloud_domain_submit = 1;
            }
        } elseif ($result === 'error') {
            $errorCode = \XLite\Core\Request::getInstance()->error_code;

            if ($errorCode === 'domain_in_use') {
                \XLite\Core\TopMessage::addError('cloud_domain_name.error.domain_in_use');
            } else {
                \XLite\Core\TopMessage::addError('An error occurred while transferring your store. Our engineers are already working on fixing it.');
            }
        }

        $this->setReturnURL($this->buildURL('cloud_domain_name'));
    }

    public function getSampleNsServers()
    {
        return [
            'lamar.ns.cloudflare.com',
            'naomi.ns.cloudflare.com'
        ];
    }

    /**
     * @return array
     */
    protected function getRegistrarsHelpUrls()
    {
        return [
            'whois.godaddy.com'          => 'https://ca.godaddy.com/help/add-an-ns-record-19212',
            'whois.networksolutions.com' => 'https://knowledge.web.com/subjects/article/KA-01114/en-us#NS',
            'whois.enom.com'             => 'https://help.enom.com/hc/en-us/articles/115000486451-Nameservers-NS-#change',
            'whois.name.com'             => 'https://www.name.com/support/articles/205934457-Registering-custom-nameservers?keyword=nameserver',
            'whois.register.com'         => 'https://knowledge.web.com/subjects/article/KA-01114/en-us#Rcom',
        ];
    }
}
