<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Flatten;

use IteratorIterator;
use Traversable;
use XCart\Bus\Domain\Module;

class Flatten extends IteratorIterator
{
    public const RULE_CORE = 'core';

    /**
     * if installed - next major version only
     * if not installed - last version
     */
    public const RULE_MAJOR = 'major';

    /**
     * if installed - next minor version or next build version or current version
     * if not installed - last version
     */
    public const RULE_MINOR = 'minor';

    /**
     * if installed - next build version or current version
     * if not installed - last version
     */
    public const RULE_BUILD = 'build';

    /**
     * if installed - last version or current version
     * if not installed - last version
     */
    public const RULE_LAST = 'last';

    /**
     * @var string
     */
    protected $rule;

    /**
     * @var string
     */
    private $coreMajorVersion;

    /**
     * @param Traversable $iterator
     * @param Module      $core
     * @param string      $rule
     */
    public function __construct(Traversable $iterator, Module $core, $rule = self::RULE_LAST)
    {
        parent::__construct($iterator);

        $this->rule = $rule;

        [$system, $major] = Module::explodeVersion($core->version);
        $this->coreMajorVersion = $system . '.' . $major;
    }

    /**
     * @param string      $name
     * @param Module|null $primary
     * @param Module|null $secondary
     * @param mixed       $default
     *
     * @return mixed|null
     */
    protected static function getField($name, ?Module $primary, ?Module $secondary = null, $default = null)
    {
        if (isset($primary) && (!empty($primary->{$name}) || is_bool($primary->{$name}))) {
            return $primary->{$name};
        }

        if (isset($secondary) && (!empty($secondary->{$name}) || is_bool($secondary->{$name}))) {
            return $secondary->{$name};
        }

        return $default;
    }

    /**
     * @return Module
     */
    public function current(): ?Module
    {
        $versions = $this->getInnerIterator()->current();

        if ($versions === null) {
            return null;
        }

        $installed = $this->getInstalled($versions);
        if (!$installed) {
            return $this->merge(null, $this->getLastVersion($versions));
        }

        switch ($this->rule) {
            case self::RULE_CORE:
                $next = $this->getNextCoreOrCurrent($installed, $versions);
                break;
            case self::RULE_MAJOR:
                $next = $this->getNextMajorOrCurrent($installed, $versions);
                break;
            case self::RULE_MINOR:
                $next = $this->getNextMinorOrBuildOrCurrent($installed, $versions);
                break;
            case self::RULE_BUILD:
                $next = $this->getNextBuildOrCurrent($installed, $versions);
                break;
            case self::RULE_LAST:
            default:
                $next = $this->getLastVersion($versions);
                break;
        }

        return $this->merge($installed, $next);
    }

    /**
     * @param Module $installed
     * @param Module $marketplace
     *
     * @return Module
     */
    protected function merge(?Module $installed, ?Module $marketplace): Module
    {
        $installedVersion   = self::getField('version', $installed);
        $marketplaceVersion = self::getField('version', $marketplace);

        $moduleData = [
            'id' => self::getField('id', $installed, $marketplace),

            'version'          => version_compare($installedVersion, $marketplaceVersion, '<')
                ? $marketplaceVersion
                : null,
            'installedVersion' => $installedVersion,

            'type' => self::getField('type', $installed, $marketplace),

            'author'      => self::getField('author', $marketplace, $installed),
            'name'        => self::getField('name', $marketplace, $installed),
            'authorName'  => self::getField('authorName', $marketplace, $installed),
            'moduleName'  => self::getField('moduleName', $marketplace, $installed),
            'description' => self::getField('description', $marketplace, $installed),

            'minorRequiredCoreVersion' => (int) self::getField('minorRequiredCoreVersion', $installed, null, 0),

            'dependsOn'        => self::getField('dependsOn', $installed, null, []),
            'incompatibleWith' => self::getField('incompatibleWith', $installed, null, []),
            'requiredBy'       => [],

            'showSettingsForm' => self::getField('showSettingsForm', $installed, null, false),

            'isSystem'   => self::getField('isSystem', $installed, $marketplace, true),
            'canDisable' => self::getField('canDisable', $installed, null, true),

            'icon'     => self::getField('icon', $marketplace, $installed),
            'listIcon' => self::getField('listIcon', $marketplace, $installed),

            'installed'     => self::getField('installed', $installed, null, false),
            'installedDate' => self::getField('installedDate', $installed, null, 0),
            'enabled'       => self::getField('enabled', $installed, null, false),
            'enabledDate'   => self::getField('enabledDate', $installed, null, 0),
            'skinPreview'   => self::getField('skinPreview', $marketplace, $installed),

            'pageUrl'       => self::getField('pageURL', $marketplace),
            'authorPageUrl' => self::getField('authorPageURL', $marketplace),
            'authorEmail'   => self::getField('authorEmail', $marketplace),

            'revisionDate' => self::getField('revisionDate', $marketplace, null, 0),
            'price'        => (float) self::getField('price', $marketplace, null, 0.0),
            'origPrice'    => (float) self::getField('origPrice', $marketplace, null, 0.0),
            'downloads'    => self::getField('downloads', $marketplace, null, 0),
            'rating'       => self::getField('rating', $marketplace, null, 0),
            'tags'         => array_map(static function ($item) {
                return ['id' => $item, 'name' => $item];
            }, self::getField('tags', $marketplace, null, [])),
            'translations' => self::getField('translations', $marketplace, null, []),

            'wave'         => self::getField('wave', $marketplace),
            'editions'     => self::getField('editions', $marketplace),
            'editionState' => self::getField('editionState', $marketplace),
            'xcnPlan'      => self::getField('xcnPlan', $marketplace),

            'actions'       => [],
            'scenarioState' => [
                'enabled'   => self::getField('enabled', $installed, null, false),
                'installed' => self::getField('installed', $installed, null, false),
            ],

            'private'         => self::getField('private', $installed, $marketplace, '0') === '1',
            'xbProductId'     => self::getField('xbProductId', $marketplace),
            'custom'          => !(bool) $marketplace,
            'purchaseUrl'     => '',
            'salesChannelPos' => self::getField('salesChannelPos', $marketplace, $installed, -1),
            'isLanding'       => self::getField('isLanding', $marketplace, $installed, false),
            'hash'            => self::getField('hash', $marketplace, null, []),
            'hasLicense'      => self::getField('hasLicense', $marketplace, null, '')
        ];

        $moduleData['landingPosition'] = $moduleData['isLanding']
            ? self::getField('landingPosition', $marketplace, $installed, 1000000)
            : 1000000;

        $moduleData['onSale'] = 0 < $moduleData['price'] && $moduleData['price'] < $moduleData['origPrice'];

        return new Module($moduleData);
    }

    /**
     * @param Module[] $data
     *
     * @return Module|null
     */
    private function getInstalled($data): ?Module
    {
        foreach ($data as $module) {
            if ($module->installed) {
                return $module;
            }
        }

        return null;
    }

    /**
     * @param Module[] $data
     *
     * @return Module|null
     */
    private function getLastVersion($data): ?Module
    {
        return array_reduce($data, function ($carry, $item) {
            /** @var Module $carry */
            /** @var Module $item */

            if (
                $item->installed
                || ($item->uploaded && $carry)
            ) {
                return $carry;
            }

            $version     = $carry->version ?? '0.0.0.0';
            $itemVersion = $item->version ?? '0.0.0.0';

            [$itemCore, $itemMajor, ,] = Module::explodeVersion($itemVersion);

            if (version_compare($version, $itemVersion, '<')) {
                if ($this->coreMajorVersion === $itemCore . '.' . $itemMajor) {
                    return $item;

                }

                if (!$carry) {
                    return $item;
                }
            }

            return $carry;
        });
    }

    /**
     * @param Module   $installed
     * @param Module[] $data
     *
     * @return Module|null
     */
    private function getCurrentVersion($installed, $data): ?Module
    {
        $installedVersion = $installed->version ?? '0.0.0.0';

        foreach ($data as $item) {
            if ($item->installed) {
                continue;
            }

            $version = $item->version ?? '0.0.0.0';

            if (version_compare($version, $installedVersion, '=')) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param Module   $installed
     * @param Module[] $data
     *
     * @return Module|null
     */
    private function getNextCoreOrCurrent($installed, $data): ?Module
    {
        return $this->getCurrentVersion($installed, $data);
    }

    /**
     * @param Module   $installed
     * @param Module[] $data
     *
     * @return Module|null
     */
    private function getNextMajorOrCurrent($installed, $data): ?Module
    {
        if (!$this->checkModuleCoreVersion($installed)) {
            return $installed;
        }

        // todo: check if installed version is the last in major branch
        $next = array_reduce($data, function ($carry, $item) {
            /** @var Module $carry */
            /** @var Module $item */

            if ($item->installed) {
                return $carry;
            }

            [$core, $major, ,] = Module::explodeVersion($carry->version);

            [$itemCore, $itemMajor, ,] = Module::explodeVersion($item->version);

            return ($itemCore === $core
                && $itemMajor > $major
                && $this->coreMajorVersion === $itemCore . '.' . $itemMajor
            )
                ? $item
                : $carry;
        }, $installed);

        return $next && $next !== $installed ? $next : $this->getCurrentVersion($installed, $data);
    }

    /**
     * @param Module   $installed
     * @param Module[] $data
     *
     * @return Module|null
     */
    private function getNextMinorOrBuildOrCurrent($installed, $data): ?Module
    {
        if (!$this->checkModuleCoreVersion($installed)) {
            return $installed;
        }

        $next = array_reduce($data, function ($carry, $item) {
            /** @var Module $carry */
            /** @var Module $item */

            if ($item->installed) {
                return $carry;
            }

            [, , $minor, $build] = Module::explodeVersion($carry->version);

            [$itemCore, $itemMajor, $itemMinor, $itemBuild] = Module::explodeVersion($item->version);

            return (($itemMinor > $minor
                    || ($itemMinor === $minor && $itemBuild > $build))
                && $this->coreMajorVersion === $itemCore . '.' . $itemMajor
            )
                ? $item
                : $carry;
        }, $installed);

        return $next && $next !== $installed ? $next : $this->getCurrentVersion($installed, $data);
    }

    /**
     * @param Module   $installed
     * @param Module[] $data
     *
     * @return Module|null
     */
    private function getNextBuildOrCurrent($installed, $data): ?Module
    {
        if (!$this->checkModuleCoreVersion($installed)) {
            return $installed;
        }

        $next = array_reduce($data, function ($carry, $item) {
            /** @var Module $carry */
            /** @var Module $item */

            if ($item->installed) {
                return $carry;
            }

            [, , $minor, $build] = Module::explodeVersion($carry->version);

            [$itemCore, $itemMajor, $itemMinor, $itemBuild] = Module::explodeVersion($item->version);

            return ($itemMinor === $minor
                && $itemBuild > $build
                && $this->coreMajorVersion === $itemCore . '.' . $itemMajor
            )
                ? $item
                : $carry;
        }, $installed);

        return $next && $next !== $installed ? $next : $this->getCurrentVersion($installed, $data);
    }

    /**
     * @param Module $module
     *
     * @return bool
     */
    private function checkModuleCoreVersion(Module $module): bool
    {
        [$moduleCore, $moduleMajor] = Module::explodeVersion($module->version);

        return $this->coreMajorVersion === $moduleCore . '.' . $moduleMajor;
    }
}
