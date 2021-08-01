<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Dashboard\Admin\InfoBlock\Notification;

use XLite;
use XLite\Core\Auth;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Marketplace;
use XLite\Core\URLManager;
use XLite\View\ModulesManager\KeysNotice;

/**
 * @ListChild (list="dashboard.info_block.notifications", weight="500", zone="admin")
 */
class LicenseWarning extends \XLite\View\Dashboard\Admin\InfoBlock\ANotification
{
    use ExecuteCachedTrait;

    /**
     * @return string
     */
    protected function getNotificationType()
    {
        return 'licenseWarning';
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' license-warning';
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'dashboard/info_block/notification/license_warning.twig';
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('License warnings');
    }

    /**
     * @return array
     */
    protected function getURLParams()
    {
        return [
            'url_params' => [
                'target'    => 'keys_notice',
                'widget'    => KeysNotice::class,
                'returnUrl' => URLManager::getCurrentURL(),
            ],
        ];
    }

    /**
     * @return int
     */
    protected function getCounter()
    {
        return $this->executeCachedRuntime(static function () {
            $unallowedModules = Marketplace::getInstance()->getInactiveContentData();
            $condition        = $unallowedModules
                && !XLite::getController()->isDisplayBlockContent()
                && (XLite::getXCNLicense() || XLite::isTrialPeriodExpired());

            return $condition ? count($unallowedModules) : 0;
        });
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getCounter();
    }

    /**
     * @return bool
     */
    protected function checkACL()
    {
        return parent::checkACL()
            && Auth::getInstance()->hasRootAccess();
    }
}
