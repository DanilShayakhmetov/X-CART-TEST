<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Dashboard\Admin\InfoBlock\Notification;

use XLite\Core\Auth;
use XLite\Core\Config;

/**
 * @ListChild (list="dashboard.info_block.notifications", weight="200", zone="admin")
 */
class SpeedUp extends \XLite\View\Dashboard\Admin\InfoBlock\ANotification
{
    /**
     * @return string
     */
    protected function getNotificationType()
    {
        return 'speedUp';
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' speed-up';
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Speed up your store');
    }

    /**
     * @return string
     */
    protected function getHeaderUrl()
    {
        return $this->buildURL('css_js_performance');
    }

    /**
     * Check if aggregate_css,aggregate_js and use_view_cache is enabled
     *
     * @return bool
     */
    protected function isAllPerformanceOptionsEnabled()
    {
        $config = Config::getInstance()->Performance;

        return $config->aggregate_css && $config->aggregate_js && $config->use_view_cache;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !$this->isAllPerformanceOptionsEnabled();
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
