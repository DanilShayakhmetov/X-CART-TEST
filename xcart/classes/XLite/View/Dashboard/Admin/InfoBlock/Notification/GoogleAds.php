<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Dashboard\Admin\InfoBlock\Notification;

use Includes\Utils\Module\Manager;
use XLite\Core\Auth;
use XLite\Core\Database;
use XLite\Core\URLManager;
use XLite\Model\Order;

/**
 * @ListChild (list="dashboard.info_block.notifications", weight="700", zone="admin")
 */
class GoogleAds extends \XLite\View\Dashboard\Admin\InfoBlock\ANotification
{
    /**
     * @return string
     */
    protected function getNotificationType()
    {
        return 'googleAds';
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' google-ads';
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Insight: Boost your sales with Google Ads addon');
    }

    /**
     * @return string
     */
    protected function getHeaderUrl()
    {
        return $this->isGoogleAdsEnabled()
            ? $this->buildURL(\XLite\Module\Kliken\GoogleAds\Logic\Helper::PAGE_SLUG)
            : URLManager::appendParamsToUrl(
                'https://market.x-cart.com/addons/google-ads-for-xcart.html',
                [
                    'utm_source'   => 'xc5admin',
                    'utm_medium'   => 'notification',
                    'utm_campaign' => 'XC5admin',
                ]
            );
    }

    /**
     * @return bool
     */
    protected function isExternal()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function isGoogleAdsEnabled()
    {
        return Manager::getRegistry()->isModuleEnabled('Kliken', 'GoogleAds');
    }

    /**
     * @return int
     */
    protected function getCompletedOrdersCount()
    {
        return Database::getRepo(Order::class)->getCompletedOrdersCount();
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getCompletedOrdersCount() >= 15;
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
