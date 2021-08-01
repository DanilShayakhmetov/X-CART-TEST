<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils\Module;

use Includes\Utils\FileManager;
use Includes\Utils\Operator;

class StructureRegistry
{
    /**
     * Get file with the modules DB structures registry file
     *
     * It has the same format as static::getDisabledStructuresPath() one
     *
     * @return string
     */
    public static function getEnabledStructuresPath()
    {
        return LC_DIR_SERVICE . '.modules.structures.registry.php';
    }

    /**
     * Get file with the HASH of modules DB structures registry file
     *
     * @return string
     */
    protected static function getEnabledStructureHashPath()
    {
        return LC_DIR_SERVICE . '.modules.structures.registry.hash.php';
    }

    /**
     * Get HASH of ENABLED registry structure
     *
     * @return string
     */
    public static function getEnabledStructureHash()
    {
        return FileManager::read(static::getEnabledStructureHashPath());
    }

    /**
     * Save HASH of ENABLED registry structure to the specific file
     *
     * @param string $hash Hash
     *
     * @return boolean
     */
    public static function saveEnabledStructureHash($hash)
    {
        return FileManager::write(static::getEnabledStructureHashPath(), $hash);
    }


    /**
     * Get disabled tables list storage path
     *
     * @return string
     */
    public static function getDisabledStructuresPath()
    {
        return LC_DIR_SERVICE . '.disabled.structures.php';
    }
    /**
     * Store DATA information in the YAML format to the file
     *
     * @param string     $path Path to the file
     * @param array|null $data Data to store in YAML
     *
     * @return void
     */
    public static function storeModuleRegistry($path, $data)
    {
        if ($data) {
            Operator::saveServiceYAML($path, $data);

        } elseif (FileManager::isExists($path)) {
            FileManager::deleteFile($path);
        }
    }

    /**
     * Store registry entry info of module into ENABLED registry
     *
     * @param string $module Module actual name
     * @param array  $data   Data to store
     *
     * @return void
     */
    public static function registerModuleToEnabledRegistry($module, $data)
    {
        $enabledPath = static::getEnabledStructuresPath();

        $enabledRegistry = Operator::loadServiceYAML($enabledPath);
        $enabledRegistry[$module] = $data;

        static::storeModuleRegistry($enabledPath, $enabledRegistry);
    }

    /**
     * Remove module information from the .disabled.structures file
     *
     * @param string $module Module actual name
     *
     * @return void
     */
    public static function removeModuleFromDisabledStructure($module)
    {
        $path = static::getDisabledStructuresPath();

        $data = Operator::loadServiceYAML($path);

        unset($data[$module]);

        static::storeModuleRegistry($path, $data);
    }

    /**
     * Move registry info entry from DISABLED registry to the ENABLED one.
     * Module must be set as ENABLED in the DB after this operation
     *
     * @param string $module Module actual name
     *
     * @return boolean Flag if the registry entry was moved
     */
    public static function moveModuleToEnabledRegistry($module)
    {
        $enabledPath = static::getEnabledStructuresPath();
        $enabledRegistry = Operator::loadServiceYAML($enabledPath);

        $disabledPath = static::getDisabledStructuresPath();
        $disabledRegistry = Operator::loadServiceYAML($disabledPath);

        $result = false;

        if (isset($disabledRegistry[$module])) {

            $enabledRegistry[$module] = $disabledRegistry[$module];
            unset($disabledRegistry[$module]);

            $result = true;
        }

        static::storeModuleRegistry($enabledPath, $enabledRegistry);
        static::storeModuleRegistry($disabledPath, $disabledRegistry);

        return $result;
    }

    /**
     * Move registry info entry from ENABLED registry to the DISABLED one.
     * Module must be set as DISABLED in the DB after this operation
     *
     * @param string $module Module actual name
     *
     * @return boolean Flag if the registry entry was moved
     */
    public static function moveModuleToDisabledRegistry($module)
    {
        $enabledPath = static::getEnabledStructuresPath();
        $enabledRegistry = Operator::loadServiceYAML($enabledPath);

        $disabledPath = static::getDisabledStructuresPath();
        $disabledRegistry = Operator::loadServiceYAML($disabledPath);

        $result = false;

        if (isset($enabledRegistry[$module])) {
            $disabledRegistry[$module] = $enabledRegistry[$module];
            $dependencies = static::getModuleDependencies($module, $enabledRegistry);
            if (!empty($dependencies)) {
                // Add self dependencies for module to avoid removing of columns add by other modules
                // if these modules will be disabled later
                $disabledRegistry[$module]['dependencies'][$module] = $dependencies;
            }
            unset($enabledRegistry[$module]);

            $result = true;
        }

        static::storeModuleRegistry($enabledPath, $enabledRegistry);
        static::storeModuleRegistry($disabledPath, $disabledRegistry);

        return $result;
    }

    /**
     * Get module dependencies from the registry
     *
     * @param string $module   Module actual name
     * @param array  $registry Modules registry
     *f
     * @return array
     */
    protected static function getModuleDependencies($module, $registry)
    {
        $result = [];

        foreach ($registry as $mod => $list) {
            if (!empty($list['dependencies'][$module]) && is_array($list['dependencies'][$module])) {
                $result = \Includes\Utils\ArrayManager::mergeRecursiveDistinct($result, $list['dependencies'][$module]);
            }
        }

        return $result;
    }
}