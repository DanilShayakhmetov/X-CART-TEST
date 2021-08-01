<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * HTTPS settings page widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class DomainNameConfirm extends \XLite\View\Dialog
{
    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            ['cloud_domain_confirm']
        );
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [$this->getDir() . '/script.js']);
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [$this->getDir() . '/style.less']);
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return 'domain_name_confirm';
    }

    /**
     * @return string
     */
    protected function getAuthCode()
    {
        return \XLite::getInstance()->getOptions(array('installer_details', 'auth_code'));
    }

    /**
     * @return string
     */
    protected function getCloudAction()
    {
        $adminHost = \XLite::getInstance()->getOptions(array('host_details', 'admin_host'));

        return $adminHost ? 'change_domain' : 'transfer';
    }
}
