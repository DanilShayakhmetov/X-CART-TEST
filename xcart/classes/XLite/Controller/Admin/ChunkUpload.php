<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Upload by chunks controller
 */
class ChunkUpload extends \XLite\Controller\Admin\AAdmin
{
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_ERROR   = 'ERROR';

    const PARAM_REDIRECT_URL   = 'redirectUrl';
    const PARAM_SUCCESS_ACTION = 'success_action';

    protected $errors = [];

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            ['process_chunk', 'success', 'abort']
        );
    }

    /**
     * Check errors and return status if empty
     *
     * @return string
     */
    protected function getStatus()
    {
        return empty($this->errors) ? self::STATUS_SUCCESS : self::STATUS_ERROR;
    }

    /**
     * Process chunk upload
     */
    public function doActionProcessChunk()
    {
        $this->set('silent', true);
        $request = \XLite\Core\Request::getInstance();

        $basename = $request->basename ?: uniqid('', true);
        $path = LC_DIR_TMP . $basename . '.filepart';

        if (($ct = file_get_contents('php://input')) === false) {
            $this->errors[] = 'Failed to open input stream';
        } else {
            if ($handle = fopen($path, 'ab')) {
                fwrite($handle, $ct);
                fclose($handle);
            } else {
                $this->errors[] = 'Failed to open output stream';
            }
        }

        echo json_encode([
            'status'   => $this->getStatus(),
            'message'  => implode('. ', $this->errors),
            'basename' => $basename,
        ]);
    }

    /**
     * Default success call
     */
    public function doActionSuccess()
    {
        $this->set('silent', true);
        $request = \XLite\Core\Request::getInstance();

        if (
            ($action = $request->{static::PARAM_SUCCESS_ACTION})
            && method_exists($this, $action . 'SuccessAction')
        ) {
            $this->{$action . 'SuccessAction'}();
        } else {
            echo json_encode([
                'status'  => $this->getStatus(),
                'message' => implode('. ', $this->errors),
            ]);
        }
    }

    /**
     * Move uploaded file to a new location
     *
     * @param $basename string Name of the file in TMP dir
     * @param $dirTo    string
     * @param $filename string
     * @param $callback callable defaultFileFilter, imagesFileFilter as example
     *
     * @return string|false New file path
     * @throws \Exception
     */
    protected function moveUploadedFile($basename, $dirTo, $filename, $callback)
    {
        $basename .= '.filepart';
        if ($basename && file_exists(LC_DIR_TMP . $basename)) {
            if (!is_callable($callback)) {
                throw new \Exception('Not callable file filter');
            } else {
                $callback(LC_DIR_TMP . $basename, $filename);
            }

            $path = \Includes\Utils\FileManager::getUniquePath($dirTo, $filename);
            return @rename(LC_DIR_TMP . $basename, $path)
                ? $path
                : false;
        }

        return false;
    }

    /**
     * Filter file
     *
     * @param $tmpPath string current file location(can be used to read file content)
     * @param $newName string desired file name
     *
     * @throws \Exception
     */
    protected function defaultFileFilter($tmpPath, $newName)
    {
        throw new \Exception('Default file filter created only for demonstration');
    }

    /**
     * Images file filter
     *
     * @param $tmpPath string current file location(can be used to read file content)
     * @param $newName string desired file name
     *
     * @throws \XLite\Core\Exception\FileValidation\AFileValidation
     */
    protected function imagesFileFilter($tmpPath, $newName)
    {
        if (!\Includes\Utils\FileManager::isImageExtension($newName)) {
            throw new \XLite\Core\Exception\FileValidation\IncorrectName;
        }

        if (!\Includes\Utils\FileManager::isImage($tmpPath)) {
            throw new \XLite\Core\Exception\FileValidation\IncorrectType;
        }
    }

    /**
     * Delete uploaded file
     *
     * @param $basename
     */
    protected function unlinkUploadedFile($basename)
    {
        $basename .= '.filepart';
        if (file_exists(LC_DIR_TMP . $basename)) {
            unlink(LC_DIR_TMP . $basename);
        }
    }

    /**
     * Default abort call
     */
    public function doActionAbort()
    {
        $this->set('silent', true);
        $request = \XLite\Core\Request::getInstance();
        $basename = $request->basename . '.filepart';
        if ($basename && file_exists(LC_DIR_TMP . $basename)) {
            unlink(LC_DIR_TMP . $basename);
        }
    }
}