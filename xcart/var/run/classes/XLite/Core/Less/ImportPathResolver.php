<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Less;


use XLite\Core\Layout;

class ImportPathResolver
{
    /**
     * @param string $filePath original less file path
     * @param string $entryDir result file directory
     *
     * @return array|null
     */
    public function getImportPathAndUri($filePath, $entryDir)
    {
        $abs = $this->normalizePath($filePath);

        if (
            ($short = $this->getResourceShortPath($abs))
            && $full = $this->getResourceFullPath($short)
        ) {
            return [
                $full,
                dirname(\Includes\Utils\FileManager::makeRelativePath(
                    $entryDir,
                    $full
                )),
            ];
        }

        return null;
    }

    /**
     * @param $path
     *
     * @return null|string
     */
    protected function getResourceShortPath($path)
    {
        foreach (Layout::getInstance()->getSkinPaths(Layout::getInstance()->getInterface()) as $skinPath) {
            if (mb_strpos($path, $skinPath['fs']) === 0) {
                return mb_substr($path, mb_strlen($skinPath['fs']) + 1);
            }
        }

        foreach (Layout::getInstance()->getSkinPaths(\XLite::COMMON_INTERFACE) as $skinPath) {
            if (mb_strpos($path, $skinPath['fs']) === 0) {
                return mb_substr($path, mb_strlen($skinPath['fs']) + 1);
            }
        }

        return null;
    }

    /**
     * @param string $shortPath
     *
     * @return string
     */
    protected function getResourceFullPath($shortPath)
    {
        return Layout::getInstance()->getResourceFullPath($shortPath, Layout::getInstance()->getInterface())
            ?: Layout::getInstance()->getResourceFullPath($shortPath, \XLite::COMMON_INTERFACE);
    }

    /**
     * @param string $filePath original less file path
     * @param string $entryDir result file directory
     *
     * @return null|array
     */
    public function getParentPathAndUri($filePath, $entryDir)
    {
        if (LC_OS_IS_WIN) {
            $filePath = realpath($filePath);
            $entryDir = realpath($entryDir);
        }

        if (@list($owner, $short) = $this->getResourceOwnerPathAndShortPath($filePath)) {
            if ($full = $this->getResourceFullParentPathByOwner($short, $owner)) {
                return [
                    $full,
                    dirname(\Includes\Utils\FileManager::makeRelativePath(
                        $entryDir,
                        $full
                    ))
                ];
            }
        }

        return null;
    }

    /**
     * @param $path
     *
     * @return array|null
     */
    protected function getResourceOwnerPathAndShortPath($path)
    {
        foreach (Layout::getInstance()->getSkinPaths(Layout::getInstance()->getInterface()) as $skinPath) {
            if (mb_strpos($path, $skinPath['fs']) === 0) {
                return [
                    $skinPath['fs'],
                    mb_substr($path, mb_strlen($skinPath['fs']) + 1),
                ];
            }
        }

        foreach (Layout::getInstance()->getSkinPaths(\XLite::COMMON_INTERFACE) as $skinPath) {
            if (mb_strpos($path, $skinPath['fs']) === 0) {
                return [
                    $skinPath['fs'],
                    mb_substr($path, mb_strlen($skinPath['fs']) + 1),
                ];
            }
        }

        return null;
    }

    /**
     * @param $shortPath
     * @param $owner
     *
     * @return null|string
     */
    protected function getResourceFullParentPathByOwner($shortPath, $owner)
    {
        $interface = Layout::getInstance()->getInterface();
        $path = $this->getExistingParentPathForInterface($shortPath, $owner, $interface);

        if (!$path && $interface !== \XLite::COMMON_INTERFACE) {
            $path = $this->getExistingParentPathForInterface($shortPath, null, \XLite::COMMON_INTERFACE);
        }

        return $path;
    }

    /**
     * @param $shortPath
     * @param $owner
     * @param $interface
     *
     * @return null|string
     */
    protected function getExistingParentPathForInterface($shortPath, $owner, $interface)
    {
        $paths = array_reduce(
            Layout::getInstance()->getSkinPaths($interface),
            function ($carry, $item) use ($owner) {
                if (!is_null($carry)) {
                    $carry[] = $item['fs'];
                } elseif (is_null($owner)) {
                    $carry = [$item['fs']];
                } elseif ($item['fs'] === $owner) {
                    return [];
                }

                return $carry;
            }
        );

        foreach ($paths as $skinPath) {
            if (file_exists($skinPath . DIRECTORY_SEPARATOR . $shortPath)) {
                return $skinPath . DIRECTORY_SEPARATOR . $shortPath;
            }
        }

        return null;
    }

    /**
     * @param $path
     *
     * @return string
     */
    protected function normalizePath($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#/+#', '/', $path);
        $data = explode('/', $path);
        $result = [];

        foreach ($data as $i => $v) {
            if (
                $v === '..'
                && count($result)
                && !in_array(end($result), ['..', '.'])
            ) {
                @array_pop($result);
            } else {
                $result[] = $v;
            }
        }

        return implode('/', $result);
    }
}