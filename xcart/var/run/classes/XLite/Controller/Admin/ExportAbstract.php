<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Auth;
use XLite\Core\Request;

/**
 * Export controller
 */
abstract class ExportAbstract extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Generator
     *
     * @var \XLite\Logic\Export\Generator
     */
    protected $generator;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Export');
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        if (Request::getInstance()->widget === 'XLite\View\PopupExport' || in_array($this->getAction(), ['itemlist_export', 'download'], true)) {
            return true;
        }

        return parent::checkACL() || Auth::getInstance()->isPermissionAllowed('manage export');
    }

    /**
     * Get generator
     *
     * @return \XLite\Logic\Export\Generator
     */
    public function getGenerator()
    {
        if (!isset($this->generator)) {
            $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());
            $this->generator = $state && isset($state['options']) ? new \XLite\Logic\Export\Generator($state['options']) : false;
        }

        return $this->generator;
    }

    /**
     * Get export state
     *
     * @return boolean
     */
    public function isExportLocked()
    {
        return \XLite\Logic\Export\Generator::isLocked();
    }

    /**
     * Export action
     *
     * @return void
     */
    protected function doActionExport()
    {
        foreach (\XLite\Core\Request::getInstance()->options as $key => $value) {
            if (
                !\XLite\Core\Config::getInstance()->Export
                || \XLite\Core\Config::getInstance()->Export->$key != $value
            ) {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                    array(
                        'category' => 'Export',
                        'name'     => $key,
                        'value'    => $value,
                    )
                );
            }
        }

        if (in_array('XLite\Logic\Export\Step\AttributeValues\AttributeValueCheckbox', \XLite\Core\Request::getInstance()->section)) {

            $addSections = array(
                'XLite\Logic\Export\Step\AttributeValues\AttributeValueSelect',
                'XLite\Logic\Export\Step\AttributeValues\AttributeValueText',
                'XLite\Logic\Export\Step\AttributeValues\AttributeValueHidden',
            );

            \XLite\Core\Request::getInstance()->section = array_merge(
                \XLite\Core\Request::getInstance()->section,
                $addSections
            );
        }

        \XLite\Logic\Export\Generator::run($this->assembleExportOptions());
    }

    /**
     * Export action
     *
     * @return void
     */
    protected function doActionItemlistExport()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        if ($state) {
            \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->removeEventState($this->getEventName());
        }

        \XLite\Logic\Export\Generator::run($this->assembleItemsListExportOptions());
        $this->setPureAction(true);
    }

    /**
     * Assemble export options
     *
     * @return array
     */
    protected function assembleExportOptions()
    {
        $request = \XLite\Core\Request::getInstance();

        return array(
            'include'       => $request->section,
            'copyResources' => 'local' == \XLite\Core\Request::getInstance()->options['files'],
            'attrs'         => $request->options['attrs'],
            'delimiter'     => isset($request->options['delimiter']) ? $request->options['delimiter'] : \XLite\Core\Config::getInstance()->Units->csv_delim,
            'charset'       => isset($request->options['charset']) ? $request->options['charset'] : \XLite\Core\Config::getInstance()->Units->export_import_charset,
            'filter'        => isset($request->options['filter']) ? $request->options['filter'] : '',
            'selection'     => isset($request->options['selection']) ? $request->options['selection'] : array(),
        );
    }

    /**
     * Assemble export options
     *
     * @return array
     */
    protected function assembleItemsListExportOptions()
    {
        $options = $this->assembleExportOptions();
        $options['itemsList'] = true;

        return $options;
    }


    /**
     * Cancel
     *
     * @return void
     */
    protected function doActionCancel()
    {
        \XLite\Logic\Export\Generator::cancel();
        \XLite\Core\TopMessage::addWarning('Export has been cancelled.');

        $this->setSilenceClose(true);
    }

    /**
     * Download
     *
     * @return void
     */
    protected function doActionDownload()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());
        $path = \XLite\Core\Request::getInstance()->path;
        if ($state && $path) {
            $generator = new \XLite\Logic\Export\Generator($state['options']);
            $list = $generator->getDownloadableFiles();

            $path = LC_DIR_VAR . $generator->getOptions()->dir . LC_DS . $path;
            if (in_array($path, $list)) {
                $name = basename($path);
                header('Content-Type: ' . $this->detectMimeType($path) . '; charset=UTF-8');
                header('Content-Disposition: attachment; filename="' . $name . '"; modification-date="' . date('r') . ';');
                header('Content-Length: ' . filesize($path));

                $this->set('silent', true);

                readfile($path);
                die(0);
            }
        }
    }

    /**
     * Delete all files
     *
     * @return void
     */
    protected function doActionDeleteFiles()
    {
        $generator = new \XLite\Logic\Export\Generator();
        $generator->deleteAllFiles();

        $this->setReturnURL($this->buildURL('export'));
    }

    /**
     * Pack and download
     *
     * @return void
     */
    protected function doActionPack()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());
        $type = \XLite\Core\Request::getInstance()->type;
        if ($state && $type) {
            $generator = new \XLite\Logic\Export\Generator($state['options']);
            $path = $generator->packFiles($type);

            if ($path) {
                $name = basename($path);
                header('Content-Type: ' . $this->detectMimeType($path) . '; charset=UTF-8');
                header('Content-Disposition: attachment; filename="' . $name . '"; modification-date="' . date('r') . ';');
                header('Content-Length: ' . filesize($path));

                readfile($path);
                die(0);
            }
        }
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('itemlist_export', 'pack', 'download', 'cancel'));
    }

    /**
     * Detect MIME type
     *
     * @param string $path File path
     *
     * @return string
     */
    protected function detectMimeType($path)
    {
        $type = 'application/octet-stream';

        if (preg_match('/\.csv$/Ss', $path)) {
            $type = 'text/csv';

        } elseif (class_exists('finfo', false)) {
            $fi = new \finfo(FILEINFO_MIME);
            $type = $fi->file($path);
        }

        return $type;
    }

    /**
     * Get event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return \XLite\Logic\Export\Generator::getEventName();
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getExportCancelFlagVarName()
    {
        return \XLite\Logic\Export\Generator::getCancelFlagVarName();
    }

    /**
     * @return string
     */
    public function printAJAXAttributes()
    {
        $result = parent::printAJAXAttributes();
        if ($this->isExportNotFinished()) {
            $result .= ' data-dialog-no-close="true"';
        }

        return $result;
    }

    /**
     * Check - export process is not-finished or not
     *
     * @return boolean
     */
    protected function isExportNotFinished()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        return $state
            && in_array($state['state'], [\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS])
            && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getExportCancelFlagVarName());
    }
}
