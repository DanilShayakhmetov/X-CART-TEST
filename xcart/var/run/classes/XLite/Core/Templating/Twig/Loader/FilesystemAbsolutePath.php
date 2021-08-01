<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating\Twig\Loader;

use Twig_Error_Loader;
use Twig_LoaderInterface;
use XLite\Core\Templating\TemplateFinderInterface;

/**
 * Filesystem loader implementation that uses absolute file paths as template names
 * (to be used by CacheManagementInterface implementations to invalidate and warmup template cache)
 */
class FilesystemAbsolutePath implements Twig_LoaderInterface
{
    private $rootPath;

    /**
     * @param string|null  $rootPath The root path common to all relative paths (null for getcwd())
     */
    public function __construct($rootPath = null)
    {
        $this->rootPath = (null === $rootPath ? getcwd() : $rootPath) . \DIRECTORY_SEPARATOR;
        if (false !== $realPath = realpath($rootPath)) {
            $this->rootPath = $realPath . \DIRECTORY_SEPARATOR;
        }
    }


    /**
     * Gets the source code of a template, given its name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The template source code
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getSource($name)
    {
        return file_get_contents($name);
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param string $path The path to template to load
     *
     * @return string The cache key
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getCacheKey($path)
    {
        $len = \strlen($this->rootPath);
        if (0 === strncmp($this->rootPath, $path, $len)) {
            return substr($path, $len);
        }

        return $path;
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string $name The template name
     * @param int    $time Timestamp of the last modification time of the
     *                     cached template
     *
     * @return bool true if the template is fresh, false otherwise
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function isFresh($name, $time)
    {
        return filemtime($name) <= $time;
    }
}