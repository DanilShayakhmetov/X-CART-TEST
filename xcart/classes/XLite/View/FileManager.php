<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;


class FileManager extends \XLite\View\AView
{
    const PARAM_DATA         = 'data';
    const PARAM_DIR          = 'dir';
    const PARAM_LINK_CLOSURE = 'link_closure';
    const PARAM_DIR_CLOSURE  = 'dir_closure';
    const PARAM_OPEN_FIRST   = 'open_first';
    const PARAM_OPEN_LAST    = 'open_last';

    const TYPE_DIR  = 'dir';
    const TYPE_FILE = 'file';

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_DATA         => new \XLite\Model\WidgetParam\TypeCollection('Files data', []),
            static::PARAM_DIR          => new \XLite\Model\WidgetParam\TypeString('Files directory', []),
            static::PARAM_LINK_CLOSURE => new \XLite\Model\WidgetParam\TypeObject('Closure for links'),
            static::PARAM_DIR_CLOSURE  => new \XLite\Model\WidgetParam\TypeObject('Closure for directory'),
            static::PARAM_OPEN_FIRST   => new \XLite\Model\WidgetParam\TypeBool('Open first file folder tree'),
            static::PARAM_OPEN_LAST    => new \XLite\Model\WidgetParam\TypeBool('Open last file folder tree'),
        ];
    }

    /**
     * @return null|\Closure
     */
    protected function getLinkClosure()
    {
        return $this->getParam(static::PARAM_LINK_CLOSURE);
    }

    /**
     * @return null|\Closure
     */
    protected function getDirClosure()
    {
        return $this->getParam(static::PARAM_DIR_CLOSURE);
    }

    protected function getDefaultTemplate()
    {
        return 'file_manager/body.twig';
    }

    protected function getCommonFiles()
    {
        return [
            static::RESOURCE_JS => static::getVueLibraries(),
        ];
    }

    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'file_manager/js/file_manager.js',
        ]);
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'file_manager/css/file_manager.less',
        ]);
    }

    /**
     * @return array
     */
    protected function getData()
    {
        return $this->getParam(static::PARAM_DATA)
            ?: static::preparePath($this->getDir());
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return $this->getParam(static::PARAM_DIR);
    }

    /**
     * @return boolean
     */
    protected function isOpenFirst()
    {
        return $this->getParam(static::PARAM_OPEN_FIRST);
    }

    /**
     * @return boolean
     */
    protected function isOpenLast()
    {
        return $this->getParam(static::PARAM_OPEN_LAST);
    }

    /**
     * @param string $path
     *
     * @param bool   $recursive
     *
     * @return array
     */
    public static function preparePath($path, $recursive = true)
    {
        $result = [];

        $dir = new \DirectoryIterator($path);

        foreach ($dir as $entry) {
            if (!$entry->isDot() && ($entry->isFile() || ($entry->isReadable() && $entry->isExecutable()))) {
                $result[] = static::prepareEntry($entry->getPathname(), $recursive);
            }
        }

        usort($result, function ($v1, $v2) {
            return $v1['name'] > $v2['name'] ? 1 : -1;
        });

        return $result;
    }

    /**
     * @param string $path
     *
     * @param bool   $recursive
     *
     * @return array
     */
    public static function prepareEntry($path, $recursive = true)
    {
        $result = [
            'name' => basename($path),
            'path' => $path,
            'type' => is_dir($path) ? static::TYPE_DIR : static::TYPE_FILE,
        ];


        if (is_dir($path)) {
            if ($recursive) {
                $result['children'] = [];

                $dir = new \DirectoryIterator($path);

                foreach ($dir as $entry) {
                    if (!$entry->isDot() && ($entry->isFile() || ($entry->isReadable() && $entry->isExecutable()))) {
                        $result['children'][] = self::prepareEntry($entry->getPathname(), $recursive);
                    }
                }

                usort($result['children'], function ($v1, $v2) {
                    if ($v1['name'] > $v2['name']) {
                        return 1;
                    } else {
                        return $v1['name'] < $v2['name'] ? -1 : 0;
                    }
                });
            } else {
                $result['loadable'] = true;
            }
        } else {
            $info = new \SplFileInfo($path);
            $result['size'] = $info->getSize();
            $result['mtime'] = $info->getMTime();
        }

        return $result;
    }
}