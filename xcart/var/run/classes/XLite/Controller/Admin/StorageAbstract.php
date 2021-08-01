<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Storage
 */
abstract class StorageAbstract extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Storage
     *
     * @var \XLite\Model\Base\Storage
     */
    protected $storage;

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && ('download' != $this->getAction() || $this->getStorage());
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || $this->checkStoragePermissions();
    }

    /**
     * Check storage permissions
     * Return true if current user can download files of this storage
     *
     * @return boolean
     */
    protected function checkStoragePermissions()
    {
        $result = false;

        $permissions = $this->getStorage() ? $this->getStorage()->getAdminPermissions() : array();

        foreach ($permissions as $perm) {
            if (\XLite\Core\Auth::getInstance()->isPermissionAllowed($perm)) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('download'));
    }

    /**
     * Download
     *
     * @return void
     */
    protected function doActionDownload()
    {
        $this->set('silent', true);
        $this->setSuppressOutput(true);
        $this->readStorage($this->getStorage());
    }

    /**
     * Get storage
     *
     * @return \XLite\Model\Base\Storage
     */
    protected function getStorage()
    {
        if (
            !isset($this->storage)
            || !is_object($this->storage)
            || !($this->storage instanceof \XLite\Model\Base\Storage)
        ) {
            $class = \XLite\Core\Request::getInstance()->storage;
            if (\XLite\Core\Operator::isClassExists($class)) {
                $id = \XLite\Core\Request::getInstance()->id;
                $this->storage = \XLite\Core\Database::getRepo($class)->find($id);
                if (!$this->storage->isFileExists()) {
                    $this->storage = null;
                }
            }
        }

        return $this->storage;
    }

    /**
     * Read storage
     *
     * @param \XLite\Model\Base\Storage $storage Storage
     */
    protected function readStorage(\XLite\Model\Base\Storage $storage)
    {
        if (
            \XLite\Core\ConfigParser::getOptions(['other', 'use_sendfile'])
            && $this->isStorageServerReadable($storage)
        ) {
            if (\Includes\Environment::isApache() || \Includes\Environment::isLighttpd()) {
                $this->readStorageXSendfile($storage);
            } elseif (\Includes\Environment::isNginx()) {
                $this->readStorageXAccelRedirect($storage);
            } else {
                $this->readStorageDefault($storage);
            }
        } elseif (
            $this->isStorageURL($storage)
        ) {
            $this->readStorageByURL($storage);
        } else {
            $this->readStorageDefault($storage);
        }
    }

    /**
     * Check if storage can be returned via headers
     *
     * @param \XLite\Model\Base\Storage $storage
     *
     * @return bool
     */
    protected function isStorageServerReadable(\XLite\Model\Base\Storage $storage)
    {
        return in_array($storage->getStorageType(), [
                \XLite\Model\Base\Storage::STORAGE_ABSOLUTE,
                \XLite\Model\Base\Storage::STORAGE_RELATIVE,
            ])
            && (
                !\Includes\Environment::isNginx()
                || strpos($storage->getStoragePath(), LC_DIR_FILES) === 0
            );
    }

    /**
     * Check if storage can be returned as URL
     *
     * @param \XLite\Model\Base\Storage $storage
     *
     * @return bool
     */
    protected function isStorageURL(\XLite\Model\Base\Storage $storage)
    {
        return $storage->getStorageType() == \XLite\Model\Base\Storage::STORAGE_URL;

    }

    /**
     * @param \XLite\Model\Base\Storage $storage
     */
    protected function readStorageByURL(\XLite\Model\Base\Storage $storage)
    {
        header("Location: ".$storage->getStoragePath());
    }

    /**
     * @param \XLite\Model\Base\Storage $storage
     */
    protected function readStorageXSendfile(\XLite\Model\Base\Storage $storage)
    {
        $path = $storage->getStoragePath();

        header("X-Sendfile: {$path}");
        header('Content-Type: ' . $storage->getMime());
        header('Content-Disposition: attachment; filename="' . addslashes($storage->getFileName()) . '";');
    }

    /**
     * @param \XLite\Model\Base\Storage $storage
     */
    protected function readStorageXAccelRedirect(\XLite\Model\Base\Storage $storage)
    {
        $uri = '/storage_download/' . str_replace('\\', '/', substr($storage->getStoragePath(), strlen(LC_DIR_FILES)));

        header("X-Accel-Redirect: {$uri}");
        header('Content-Type: ' . $storage->getMime());
        header('Content-Disposition: attachment; filename="' . addslashes($storage->getFileName()) . '";');
    }

    /**
     * @param \XLite\Model\Base\Storage $storage Storage
     */
    protected function readStorageDefault(\XLite\Model\Base\Storage $storage)
    {
        header('Content-Type: ' . $storage->getMime());
        header('Content-Size: ' . $storage->getSize());
        header('Content-Disposition: attachment; filename="' . addslashes($storage->getFileName()) . '";');
        $range = null;

        if (isset($_SERVER['HTTP_RANGE'])) {
            list($sizeUnit, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if ('bytes' == $sizeUnit) {
                list($range, $extra) = explode(',', $range, 2);
            }
        }

        $start = null;
        $length = $storage->getSize();

        if ($range) {
            $size = $length;
            list($start, $end) = explode('-', $range, 2);
            $start = abs(intval($start));
            $end = abs(intval($end));

            $end = $end ? min($end, $size - 1) : ($size - 1);
            $start = (!$start || $end < $start) ? 0 : max($start, 0);

            if (0 < $start || ($size - 1) > $end) {
                header('HTTP/1.2 206 Partial Content', true, 206);
            }

            header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
            $length = ($end - $start + 1);
        }

        header('Accept-Ranges: bytes');
        header('Content-Length: ' . $length);

        if (!\XLite\Core\Request::getInstance()->isHead()) {
            $storage->readOutput($start, $length);
        }
    }
}
