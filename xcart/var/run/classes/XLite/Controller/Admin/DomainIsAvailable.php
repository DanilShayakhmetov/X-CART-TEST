<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Logic\WhoisService;

class DomainIsAvailable extends \XLite\Controller\Admin\AAdmin
{
    public static function needFormId()
    {
        return false;
    }

    public function getTitle()
    {
        return static::t('domain is on sale', [
            'domain' => \XLite\Core\Request::getInstance()->domain_name
        ]);
    }

    public function checkAccess()
    {
        return parent::checkAccess()
            && \XLite::getInstance()->getOptions(['service', 'is_cloud'])
            && !\XLite::getInstance()->getOptions(['service', 'is_trial']);
    }

    public function doActionCheckDomain()
    {
        $domainName = \XLite\Core\Request::getInstance()->domain_name;

        $status = [];

        if ($domainName) {
            try {
                $status = WhoisService::create($domainName)->getStatus();

                if (isset($status['errCode'])) {
                    $status['isInlineError'] = ($status['errCode'] === 'WHOIS_01');
                }
            } catch (\Exception $exception) {
                \XLite\Logger::getInstance()->registerException($exception);
            }
        }

        $this->displayJSON($status);
        die;
    }
}