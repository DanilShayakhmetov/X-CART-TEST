<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XLite\Core\Cache\ExecuteCached;

/**
 * Upgrade top box
 *
 * @ListChild (list="admin.main.page.header_wrapper", weight="1000", zone="admin")
 */
class UpgradeTopBox extends \XLite\View\AView
{
    /**
     * Key for storing tmpVars read mark
     */
    const READ_MARK_KEY = 'toplinksMenuReadHash';
    /**
     * Flags
     *
     * @var array
     */
    protected $updateFlags;

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'top_links/version_notes/parts/upgrade.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'top_links/version_notes/parts/upgrade.twig';
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    protected function checkACL()
    {
        return parent::checkACL()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * Return list of disallowed targets
     *
     * @return string[]
     */
    public static function getDisallowedTargets()
    {
        return ['upgrade'];
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Auth::getInstance()->isAdmin()
            && \XLite\Core\Marketplace::getInstance()->getUpgradeTypesEntries();
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
        $modulesHash = \XLite\Core\Marketplace::getInstance()->getHashMap($withCore);

        return isset($modulesHash[$type])
            ? $modulesHash[$type]
            : 0;
    }

    /**
     * @param      $type
     * @param bool $withCore
     *
     * @return bool
     */
    protected function hasEntriesOfType($type, $withCore = true)
    {
        return $this->getCountOfType($type, $withCore) > 0;
    }

    /**
     * @return bool
     */
    protected function hasHotfixes()
    {
        return $this->hasEntriesOfType('build');
    }

    /**
     * @return bool
     */
    protected function hasUpdates()
    {
        return $this->hasEntriesOfType('minor');
    }

    /**
     * @return bool
     */
    protected function hasUpgrades()
    {
        $entries = \XLite\Core\Marketplace::getInstance()->getUpgradeTypesEntries();

        $hasCoreUpgrade = false;
        if (!$this->hasEntriesOfType('build') && !$this->hasEntriesOfType('minor')) {
            return !empty($entries['major']['CDev-Core']['type'])
                && $entries['major']['CDev-Core']['type'] === 'major';
        }

        return $hasCoreUpgrade;
    }

    /**
     * Return true if box should be active
     *
     * @return boolean
     */
    protected function isUpgradeBoxVisible()
    {
        return $this->isCoreUpgradeAvailable() || $this->areUpdatesAvailable();
    }

    /**
     * Check if there is a new core version
     *
     * @return boolean
     */
    protected function isCoreUpgradeAvailable()
    {
        return (boolean) $this->getCoreUpgradeTypes();
    }

    /**
     * Check if there is a new core version
     *
     * @return boolean
     */
    protected function isSelfUpgradeAvailable()
    {
        return (boolean) $this->getCoreUpgradeTypes();
    }

    /**
     * Check if there is a new core version
     *
     * @return array
     */
    protected function getCoreUpgradeTypes()
    {
        $map = \XLite\Core\Marketplace::getInstance()->getHashMap();

        return $map['core-types'];
    }

    /**
     * Check if there is a new core version
     *
     * @param string $type
     *
     * @return array
     */
    protected function hasCoreUpgradeType($type)
    {
        $map = \XLite\Core\Marketplace::getInstance()->getHashMap();

        return in_array($type, $map['core-types'], true);
    }

    /**
     * Check if there are updates (new core revision and/or module revisions)
     *
     * @return boolean
     */
    protected function areUpdatesAvailable()
    {
        return $this->hasEntriesOfType('minor', false) || $this->hasEntriesOfType('build', false);
    }

    /**
     * Get container tag attributes
     *
     * @return array
     */
    protected function getContainerTagAttributes()
    {
        $data   = [];
        $data[] = 'upgrade-box';

        $state      = 'opened';
        $tmpVarHash = \XLite\Core\TmpVars::getInstance()->{static::READ_MARK_KEY};
        $realHash   = \XLite\Core\Marketplace::getInstance()->unseenUpdatesHash();
        if ($realHash !== $tmpVarHash) {
            \XLite\Core\TmpVars::getInstance()->{static::READ_MARK_KEY} = null;

        } elseif (!empty($tmpVarHash)) {
            $state = 'post-closed';
        }

        $data[] = $state;

        if (!$this->isUpgradeBoxVisible()) {
            $data[] = 'invisible';
        }

        if ($this->isHotfixMode()) {
            $data[] = 'hotfix';
        }

        if ($this->hasEntriesOfType('minor')) {
            $data[] = 'update';
        }

        return [
            'class' => $data,
        ];
    }

    /**
     * @return bool
     */
    protected function isHotfixMode()
    {
        return $this->hasEntriesOfType('build') || $this->hasCoreUpgradeType('build');
    }
    /**
     * @return string
     */
    protected function getDescription()
    {
        $coreAvailable = false;

        if ($this->isCoreUpgradeAvailable()) {
            $coreAvailable = $this->isHotfixMode()
                ? $this->hasCoreUpgradeType('build')
                : !$this->hasCoreUpgradeType('build');
        }

        $totalModulesCount = $this->isHotfixMode()
            ? $this->getCountOfType('build', false)
            : $this->getCountOfType('total', false) - $this->getCountOfType('build', false);

        $entries = \XLite\Core\Marketplace::getInstance()->getUpgradeTypesEntries();

        if ($entries['self']) {
            $result = static::t('Marketplace');

        } elseif ($coreAvailable && $totalModulesCount) {
            $result = static::t('new core and X addons', ['count' => $totalModulesCount]);

        } elseif ($totalModulesCount) {
            $result = static::t('X addons', ['count' => $totalModulesCount]);

        } else {
            $result = static::t('new core');
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getUpgradeUrl()
    {
        $entries = \XLite\Core\Marketplace::getInstance()->getUpgradeTypesEntries();
        if ($entries['self']
            || ($this->hasHotfixes() && $this->hasUpdates())
        ) {
            return \XLite::getInstance()->getServiceURL('#/upgrade/');
        }

        $type = $this->isHotfixMode()
            ? 'build'
            : 'minor';

        return \XLite::getInstance()->getServiceURL('#/upgrade-details/' . $type);
    }
}
