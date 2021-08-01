<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Upgrade;

use XCart\Bus\Domain\Module;
use XCart\Bus\Helper\HookFilter;

class UpgradeEntry
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var Module
     */
    public $entry;

    /**
     * @var bool
     */
    public $canUpgrade;

    /**
     * @var HookFilter
     */
    private $hookFilter;

    /**
     * @param string     $id
     * @param Module     $entry
     * @param HookFilter $hookFilter
     */
    public function __construct(
        $id,
        Module $entry,
        HookFilter $hookFilter
    ) {
        $this->hookFilter = $hookFilter;

        $this->id    = $id;
        $this->entry = $entry;

        $this->type       = $this->calculateType();
        $this->canUpgrade = $this->entry->enabled || !$this->hasHooks() || $this->type === 'major';
    }

    /**
     * @return string
     */
    private function calculateType(): string
    {
        $entry = $this->entry;

        [$system, $major, $minor, $build] = Module::explodeVersion($entry->version);
        [$installedSystem, $installedMajor, $installedMinor, $installedBuild]
            = Module::explodeVersion($entry->installedVersion);

        if ($system !== $installedSystem) {
            return 'system';
        }

        if ($major !== $installedMajor) {
            return 'major';
        }

        if ($minor !== $installedMinor) {
            return 'minor';
        }

        if ($build !== $installedBuild) {
            return 'build';
        }

        return '';
    }

    /**
     * @return bool
     */
    private function hasHooks(): bool
    {
        return $this->hookFilter->hasHooks(
            array_keys($this->entry['hash']),
            $this->id,
            $this->entry['installedVersion'],
            $this->entry['version']
        );
    }
}
