<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FileManager;


class Line extends \XLite\View\AView
{
    const PARAM_DATA         = 'data';
    const PARAM_LINK_CLOSURE = 'link_closure';
    const PARAM_DIR_CLOSURE  = 'dir_closure';

    protected function getDefaultTemplate()
    {
        return 'file_manager/line.twig';
    }

    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'file_manager/js/line.js',
        ]);
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_DATA         => new \XLite\Model\WidgetParam\TypeCollection('Data', []),
            static::PARAM_LINK_CLOSURE => new \XLite\Model\WidgetParam\TypeObject('Closure for links'),
            static::PARAM_DIR_CLOSURE  => new \XLite\Model\WidgetParam\TypeObject('Closure for directory'),
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

    /**
     * @return bool
     */
    protected function isDir()
    {
        return $this->getParam(static::PARAM_DATA)['type'] === \XLite\View\FileManager::TYPE_DIR;
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return $this->getParam(static::PARAM_DATA)['name'];
    }

    /**
     * @return string
     */
    protected function getPath()
    {
        return $this->getParam(static::PARAM_DATA)['path'];
    }

    /**
     * @return array
     */
    protected function getChildren()
    {
        return $this->getParam(static::PARAM_DATA)['children'] ?: [];
    }

    /**
     * @return string
     */
    protected function getSize()
    {
        return \XLite\Core\Converter::formatFileSize($this->getParam(static::PARAM_DATA)['size'], ' ');
    }

    /**
     * @return string
     */
    protected function getMtime()
    {
        return $this->getParam(static::PARAM_DATA)['mtime']
            ? \XLite\Core\Converter::formatTime($this->getParam(static::PARAM_DATA)['mtime'])
            : '';
    }

    /**
     * @return string
     */
    protected function getLoadable()
    {
        return !empty($this->getParam(static::PARAM_DATA)['loadable']);
    }

    /**
     * @return string
     */
    protected function getLink()
    {
        $closure = $this->isDir()
            ? $this->getParam(static::PARAM_DIR_CLOSURE)
            : $this->getParam(static::PARAM_LINK_CLOSURE);

        return $closure
            ? $closure($this->getPath(), 'download')
            : '';
    }

    /**
     * @return string
     */
    protected function getViewLink()
    {
        $closure = $this->isDir()
            ? $this->getParam(static::PARAM_DIR_CLOSURE)
            : $this->getParam(static::PARAM_LINK_CLOSURE);

        return $closure
            ? $closure($this->getPath())
            : '';
    }
}
