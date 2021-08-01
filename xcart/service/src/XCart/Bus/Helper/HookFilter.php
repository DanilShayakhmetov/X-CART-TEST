<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Helper;

use XCart\Bus\Domain\Module;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class HookFilter
{
    /**
     * @var array
     */
    public $types = ['pre_upgrade', 'post_upgrade', 'post_rebuild', 'post_rollback'];

    /**
     * @param string[] $list
     * @param string   $moduleId
     * @param string   $versionBefore
     * @param string   $versionAfter
     *
     * @return bool
     */
    public function hasHooks($list, $moduleId, $versionBefore, $versionAfter): bool
    {
        if (!$list) {
            return false;
        }

        foreach ($this->types as $type) {
            if ($this->filterHooksByType(
                $list,
                $type,
                $moduleId,
                $versionBefore,
                $versionAfter
            )) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[] $list
     * @param string   $type
     * @param string   $moduleId
     * @param string   $versionBefore
     * @param string   $versionAfter
     *
     * @return array
     */
    public function filterHooksByType($list, $type, $moduleId, $versionBefore, $versionAfter): array
    {
        if ($moduleId === 'CDev-Core') {
            $hooksDir = 'upgrade/';

        } else {
            [$author, $name] = Module::explodeModuleId($moduleId);

            $hooksDir = sprintf('classes/XLite/Module/%s/%s/hooks/upgrade/', $author, $name);
        }

        return array_values(
            array_filter($list, static function ($file) use ($type, $hooksDir, $versionBefore, $versionAfter) {
                if (!preg_match('/' . preg_quote('/' . $type, '/') . '.*\.php$/', $file)) {
                    return false;
                }

                $version = str_replace(['/', '\\'], '.', dirname(str_replace($hooksDir, '', $file)));

                return version_compare($version, $versionAfter, '<=')
                    && version_compare($version, $versionBefore, '>');
            })
        );
    }

    /**
     * @param array $list
     *
     * @return array
     */
    public function sortAscending(array $list): array
    {
        usort($list, static function ($a, $b) {
            $versionA = preg_replace('#.*(\d+\.\d+)/(\d+(\.\d+)?)/[^/]+$#S', '$1.$2', $a);
            $versionB = preg_replace('#.*(\d+\.\d+)/(\d+(\.\d+)?)/[^/]+$#S', '$1.$2', $b);

            $result = version_compare($versionA, $versionB);
            if ($result === 0) {


                $result = strnatcmp($a, $b);
            }

            return $result;
        });

        return $list;
    }

    /**
     * @param array $list
     *
     * @return array
     */
    public function sortDescending(array $list): array
    {
        return array_reverse($this->sortAscending($list));
    }
}
