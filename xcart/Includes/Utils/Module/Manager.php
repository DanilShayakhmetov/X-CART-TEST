<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils\Module;

use Includes\Decorator\Plugin\Doctrine\Utils\FixturesManager;
use MJS\TopSort\Implementations\StringSort;
use XLite\Logger;

class Manager
{
    /**
     * @var Registry
     */
    private static $registry;

    /**
     * @var IStorage
     */
    private static $storage;

    /**
     * @return Registry
     */
    public static function getRegistry()
    {
        if (self::$registry === null) {
            self::$registry = new Registry(self::getStorage());
        }

        return self::$registry;
    }

    /**
     * @return Storage
     */
    public static function getStorage()
    {
        if (self::$storage === null) {
            self::$storage = new Storage();
        }

        return self::$storage;
    }

    public static function loadModules()
    {
        try {
            $moduleIds = static::getRegistry()->getEnabledModuleIds();
            $sorted = static::sortModulesByDependency($moduleIds);

            // todo: load Main.php
            foreach ($sorted as $moduleId) {
                Module::callMainClassMethod($moduleId, 'getModuleName');
            }
        } catch (\Exception $e) {
            Logger::getInstance()->logPostponed($e->getMessage(), LOG_ERR, $e->getTraceAsString());
        }
    }

    public static function initModules()
    {
        try {
            $moduleIds = static::getRegistry()->getEnabledModuleIds();
            $sorted = static::sortModulesByDependency($moduleIds);

            foreach ($sorted as $moduleId) {
                $result = Module::callMainClassMethod($moduleId, 'init');

                if (!$result) {
                    Manager::getRegistry()->initModuleManually($moduleId);
                }
            }
        } catch (\Exception $e) {
            Logger::getInstance()->logPostponed($e->getMessage(), LOG_ERR, $e->getTraceAsString());
        }
    }

    /**
     * @param array $ids
     *
     * @return array
     * @throws \MJS\TopSort\ElementNotFoundException
     * @throws \MJS\TopSort\CircularDependencyException
     */
    public static function sortModulesByDependency(array $ids)
    {
        $sorter = new StringSort();

        foreach ($ids as $id) {
            $module = static::getRegistry()->getModule($id);
            $sorter->add($id, array_filter($module->dependsOn));
        }

        return $sorter->sort();
    }

    /**
     * Updates internal modules list
     *
     * @param array $list
     * @param array $integratedList
     */
    public static function saveModulesToStorage($list, $integratedList = [])
    {
        unset($list['CDev']['Core']);
        static::getRegistry()->updateModules($list, $integratedList);

        FixturesManager::setFixtures(
            static::getRegistry()->getNonLoadedYamlFiles()
        );
    }
    
    /**
     * @param $classesDir
     * @return string
     */
    public static function getPathPatternForPHP($classesDir)
    {
        $root = preg_quote($classesDir, '/') . 'XLite';
        $dsQuoted = preg_quote(\LC_DS, '/');

        $registry = static::getRegistry();
        $enabledModules = array_map(function ($item) use ($dsQuoted) {
            list($author, $name) = Module::explodeModuleId($item);

            return $author . $dsQuoted . $name;
        }, $registry->getEnabledModuleIds());

        $modules = '(' . implode('|', $enabledModules) . ')';

        return '/^(?:'
            . $root . $dsQuoted . '((?!Module)[a-zA-Z0-9]+)' . $dsQuoted . '.+'
            . '|' . $root . $dsQuoted . 'Module' . $dsQuoted . $modules . $dsQuoted . '.+'
            . '|' . $root
            . '|' . $root . $dsQuoted . 'Module' . $dsQuoted . '[a-zA-Z0-9]+'
            . '|' . $root . $dsQuoted . '[a-zA-Z0-9]+'
            . ')\.php$/Ss';
    }

    /**
     * @param $skinsDir
     * @return string
     */
    public static function getPathPatternForTemplates($skinsDir)
    {
        $skinsDir = preg_quote($skinsDir, '/');
        $dsQuoted = preg_quote(\LC_DS, '/');

        $modulePattern = 'modules' . $dsQuoted . ($placeholder = '@') . '(' . $dsQuoted . '|$)';

        $registry = static::getRegistry();
        $enabledModules = array_map(function ($item) use ($registry, $dsQuoted) {
            list($author, $name) = Module::explodeModuleId($item);

            return $author . $dsQuoted . $name;
        }, $registry->getEnabledModuleIds());

        return '/^' . $skinsDir . '\w+' . '(.((?!' . str_replace($placeholder, '\w+', $modulePattern) . ')|'
            . str_replace($placeholder, '(' . implode('|', $enabledModules) . ')', $modulePattern)
            . '))*\.twig$/i';
    }
}
