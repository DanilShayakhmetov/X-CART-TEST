<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Log getter controller
 */
class Log extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess();
    }

    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            [
                'view',
                'download'
            ]
        );
    }

    /**
     * Get log path
     *
     * @return string
     */
    public function getLogPath()
    {
        $path = \XLite\Core\Request::getInstance()->log;
        if ($path && !preg_match(\XLite\Logger::LOG_FILE_NAME_PATTERN, $path)) {
            $path = null;
        }
        $path = $path ? (LC_DIR_LOG . $path) : null;

        return (!$path || !file_exists($path) || !is_readable($path)) ? null : $path;
    }

    /**
     * Preprocessor for no-action ren
     *
     * @return void
     */
    protected function doNoAction()
    {
        $this->doActionView();
    }

    protected function doActionView()
    {
        $this->silent = true;

        $path = $this->getLogPath();

        header('Content-Type: text/plain');

        if ($path) {
            readfile($path);
        } else {
            echo 'Log file doesn\'t exists';
        }

        $this->setSuppressOutput(true);
    }

    protected function doActionDownload()
    {
        $this->silent = true;

        $path = $this->getLogPath();

        header('Content-Length: ' . filesize($path));
        header('Content-Type: text/plain');
        header(
            'Content-Disposition: attachment;'
            . ' filename="' . substr(basename($path), 0, -4) . '.txt";'
            . ' modification-date="' . date('r', filemtime($path)) . ';'
        );

        readfile($path);
    }

}
