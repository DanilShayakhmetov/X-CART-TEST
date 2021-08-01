<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Dashboard\Admin\InfoBlock\Notification;

use XLite;
use XLite\Core\Auth;
use XLite\Core\Marketplace;
use XLite\Core\TmpVars;

/**
 * @ListChild (list="dashboard.info_block.notifications", weight="400", zone="admin")
 */
class Upgrade extends \XLite\View\Dashboard\Admin\InfoBlock\ANotification
{
    const STATUS_NO_UPDATES      = '';
    const STATUS_UPDATES_PRESENT = 'updates';
    const STATUS_UPGRADE_PRESENT = 'upgrade';

    /**
     * @var array
     */
    protected $status;

    /**
     * @return string
     */
    protected function getNotificationType()
    {
        return 'upgradeInfo';
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' upgrade-info';
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        if ($this->isAppStoreUpdateAvailable()) {
            return static::t('App Store update is available');
        }

        return ($this->isCoreUpgradeAvailable() && !$this->areUpdatesAvailable())
            ? static::t('Upgrade available')
            : static::t('Updates are available');
    }

    /**
     * @return string
     */
    protected function getHeaderUrl()
    {
        $entries = Marketplace::getInstance()->getUpgradeTypesEntries();

        if ($entries['self']
            || ($entries['build'] && $entries['minor'])
        ) {
            return XLite::getInstance()->getServiceURL('#/upgrade/');
        }

        return $entries['build']
            ? XLite::getInstance()->getServiceURL('#/upgrade-details/build')
            : XLite::getInstance()->getServiceURL('#/upgrade-details/minor');
    }

    /**
     * Check if there is a new core version
     *
     * @return boolean
     */
    protected function isCoreUpgradeAvailable()
    {
        $map = Marketplace::getInstance()->getHashMap();

        return $map['core-types'];
    }

    /**
     * Check if there is a new App Store version
     *
     * @return boolean
     */
    protected function isAppStoreUpdateAvailable()
    {
        $entries = \XLite\Core\Marketplace::getInstance()->getUpgradeTypesEntries();

        return $entries['self'];
    }


    /**
     * Check if there are updates (new core revision and/or module revisions)
     *
     * @return boolean
     */
    protected function areUpdatesAvailable()
    {
        return $this->getCountOfType('minor', false) || $this->getCountOfType('build', false);
    }

    /**
     * @param string $type
     *
     * @param bool   $withCore
     *
     * @return int
     */
    protected function getCountOfType($type, $withCore = true)
    {
        $modulesHash = Marketplace::getInstance()->getHashMap($withCore);

        return isset($modulesHash[$type])
            ? $modulesHash[$type]
            : 0;
    }

    /**
     * Return update timestamp
     *
     * @return integer
     */
    protected function getLastUpdateTimestamp()
    {
        $result = TmpVars::getInstance()->upgradeInfoUpdateTimestamp;
        $status = $this->calcUpgradeInfoStatus();

        if (!isset($result) || $this->isStatusChanged($status)) {
            $result = LC_START_TIME;
            $this->setLastUpdateTimestamp($result);
            TmpVars::getInstance()->upgradeInfoStatus = $status;
        }

        return $result;
    }

    /**
     * Check status changed
     *
     * @param array $status Current status
     *
     * @return boolean
     */
    protected function isStatusChanged($status)
    {
        $result      = false;
        $savedStatus = TmpVars::getInstance()->upgradeInfoStatus;

        if (!is_array($savedStatus) || !isset($savedStatus['status'])) {
            $result = true;
        }

        if ($savedStatus['status'] !== $status['status']
            || $savedStatus['count'] !== $status['count']
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * Returns calculated status
     *
     * @return array
     */
    protected function getStatus()
    {
        if (null === $this->status) {
            $this->status = $this->calcUpgradeInfoStatus();
        }

        return $this->status;
    }

    /**
     * Return upgrade status
     *
     * @return array
     */
    protected function calcUpgradeInfoStatus()
    {
        $status = $this->isCoreUpgradeAvailable()
            ? static::STATUS_UPGRADE_PRESENT
            : ($this->areUpdatesAvailable() ? static::STATUS_UPDATES_PRESENT : static::STATUS_NO_UPDATES);

        return [
            'status' => $status,
            'count'  => $status === static::STATUS_NO_UPDATES
                ? 0
                : $this->getCounter(),
        ];
    }

    /**
     * Get entries count
     *
     * @return integer
     */
    protected function getCounter()
    {
        if ($this->isAppStoreUpdateAvailable()) {
            return 0;
        }

        return $this->getCountOfType('build') ?: $this->getCountOfType('minor');
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        if (parent::isVisible()) {
            $status = $this->getStatus();

            return $status['status'] !== static::STATUS_NO_UPDATES;
        }

        return false;
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
