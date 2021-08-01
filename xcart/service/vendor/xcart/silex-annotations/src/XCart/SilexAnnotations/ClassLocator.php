<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations;

use Closure;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

class ClassLocator
{
    /**
     * @var string
     */
    private $root;

    /**
     * @return Closure
     */
    private static function getPHPFileFilter()
    {
        /**
         * @param SplFileInfo                $node
         * @param string                     $key
         * @param RecursiveDirectoryIterator $iterator
         *
         * @return bool
         */
        return function (
            $node,
            /** @noinspection PhpUnusedParameterInspection */
            $key,
            $iterator
        ) {
            return !$iterator->isFile() || $node->getExtension() === 'php';
        };
    }

    /**
     * @return Closure
     */
    private static function getClassFilter()
    {
        /**
         * @param string $className
         *
         * @return bool
         */
        return function ($className) {
            try {

                return class_exists($className);

            } catch (RuntimeException $e) {

                return false;
            }
        };
    }

    /**
     * @param string $root PSR-4 root path
     */
    public function __construct($root)
    {
        $this->root = realpath($root);
    }

    /**
     * @param string|string[] $locations
     *
     * @return string[]
     */
    public function getClasses($locations)
    {
        $phpFiles = [[]];
        foreach ((array) $locations as $location) {
            $phpFiles[] = array_map('realpath', array_keys(iterator_to_array($this->searchPHPFiles($location))));
        }

        return $this->searchClasses(array_unique(array_merge(...$phpFiles)));
    }

    /**
     * @param string[] $files
     *
     * @return array
     */
    private function searchClasses($files)
    {
        return array_filter(
            array_values(array_map($this->getClassNameSuggester(), $files)),
            static::getClassFilter()
        );
    }

    /**
     * @param string $location
     *
     * @return RecursiveIteratorIterator
     */
    private function searchPHPFiles($location)
    {
        return new RecursiveIteratorIterator(
            new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator(
                    $location,
                    RecursiveDirectoryIterator::SKIP_DOTS
                ),
                static::getPHPFileFilter()
            )
        );
    }

    /**
     * @return Closure
     */
    private function getClassNameSuggester()
    {
        /**
         * @param string $path
         *
         * @return string
         */
        return function ($path) {
            return str_replace(
                [$this->root . \DIRECTORY_SEPARATOR, \DIRECTORY_SEPARATOR, '.php'],
                ['', '\\', ''],
                $path
            );
        };
    }
}