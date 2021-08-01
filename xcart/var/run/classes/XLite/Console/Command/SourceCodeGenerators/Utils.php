<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\SourceCodeGenerators;

use Includes\Utils\FileManager;

class Utils
{
    /**
     * Get class short name
     *
     * @param string $class Class full name
     *
     * @return string
     */
    public static function getClassShortName($class)
    {
        $parts = explode('\\', $class);

        return array_pop($parts);
    }

    /**
     * Convert camel case string to human readable string
     *
     * @param string $camel Camel case string
     *
     * @return string
     */
    public static function convertCamelToHumanReadable($camel)
    {
        return preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $camel);
    }

    /**
     * @param $fullyQualifiedName
     * @param $content
     *
     * @return bool|string
     */
    public static function saveClass($fullyQualifiedName, $content)
    {
        $path = static::buildClassPath($fullyQualifiedName);
        FileManager::mkdirRecursive(FileManager::getDir($path));

        $result = file_put_contents($path, $content);

        return $result
            ? $path
            : false;
    }

    /**
     * @param $fullyQualifiedName
     *
     * @return string
     */
    public static function buildClassPath($fullyQualifiedName)
    {
        return rtrim(LC_DIR_CLASSES, LC_DS) . str_replace('\\', LC_DS, $fullyQualifiedName) . '.php';
    }

    /**
     * @param        $interface
     * @param        $path
     * @param        $content
     * @param string $lang
     *
     * @return bool|string
     */
    public static function saveSkinsFile($interface, $path, $content, $lang = 'en')
    {
        $path = LC_DIR_SKINS
            . $interface . LC_DS . $lang
            . LC_DS . $path;

        return static::saveSkinsFileByPath($path, $content);
    }

    /**
     * @param $path
     * @param $content
     *
     * @return bool|string
     */
    public static function saveSkinsFileByPath($path, $content)
    {
        $path = LC_DIR_SKINS . $path;

        FileManager::mkdirRecursive(FileManager::getDir($path));

        $result = file_put_contents($path, $content);

        return $result
            ? $path
            : false;
    }
}
