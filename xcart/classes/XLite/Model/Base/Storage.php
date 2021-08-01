<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

use XLite\Core\RemoteResource\IURL;
use XLite\Core\RemoteResource\RemoteResourceException;
use XLite\Core\RemoteResource\RemoteResourceFactory;

/**
 * Storage abstract store
 *
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 */
abstract class Storage extends \XLite\Model\AEntity
{
    /**
     * Storage type codes
     */
    const STORAGE_RELATIVE = 'r';
    const STORAGE_ABSOLUTE = 'f';
    const STORAGE_URL      = 'u';

    /**
     * MIME type to extenstion translation table
     *
     * @var array
     */
    protected static $types = [];

    /**
     * Unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Path (URL or file name in storage directory)
     *
     * @var string
     *
     * @Column (type="string", length=512)
     */
    protected $path;

    /**
     * File name
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $fileName = '';

    /**
     * MIME type
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $mime = 'application/octet-stream';

    /**
     * Storage type
     *
     * @var string
     *
     * @Column (type="string", length=1)
     */
    protected $storageType = self::STORAGE_RELATIVE;

    /**
     * Size
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $size = 0;

    /**
     * Create / modify date (UNIX timestamp)
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $date = 0;

    /**
     * Load error code
     *
     * @var string
     */
    protected $loadError;

    /**
     * Load error message
     *
     * @var array
     */
    protected $loadErrorMessage;

    // {{{ Getters / setters

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set mime
     *
     * @param string $mime
     *
     * @return $this
     */
    public function setMime($mime)
    {
        $this->mime = $mime;
        return $this;
    }

    /**
     * Get mime
     *
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Set storageType
     *
     * @param string $storageType
     *
     * @return $this
     */
    public function setStorageType($storageType)
    {
        $this->storageType = $storageType;
        return $this;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set date
     *
     * @param integer $date
     *
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        if ($this->isURL()) {
            $body = \XLite\Core\Operator::getURLContent($this->getPath());

        } else {
            $body = \Includes\Utils\FileManager::read($this->getStoragePath());
        }

        return $body;
    }

    /**
     * Get storage type
     *
     * @return string
     */
    public function getStorageType()
    {
        if (!$this->storageType) {
            $this->storageType = $this->isURL($this->getPath())
                ? static::STORAGE_URL
                : static::STORAGE_RELATIVE;
        }

        return $this->storageType;
    }

    /**
     * Read output
     *
     * @param integer $start  Start popsition
     * @param integer $length Length
     *
     * @return boolean
     */
    public function readOutput($start = null, $length = null)
    {
        $result = true;

        if ($this->isURL()) {
            $handle = @fopen($this->getPath(), 'rb');
        } else {
            $handle = @fopen($this->getStoragePath(), 'rb');
        }

        if ($handle) {
            if (null !== $start) {
                fseek($handle, $start);
            }

            if (isset($length)) {
                while (!feof($handle) && $length > 0) {
                    $l = min(8192, $length);
                    print fread($handle, $l);
                    flush();
                    ob_flush();
                    $length -= 8192;
                }
            } else {
                while (!feof($handle)) {
                    print fread($handle, 8192);
                    flush();
                    ob_flush();
                }
            }

            fclose($handle);
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Check if file exists
     *
     * @param string  $path      Path to check OPTIONAL
     * @param boolean $forceFile Flag OPTIONAL
     *
     * @return boolean
     */
    public function isFileExists($path = null, $forceFile = false)
    {
        if ($this->isURL($path) && !$forceFile) {
            $headers = \XLite\Core\Operator::checkURLAvailability($path ?: $this->getPath());
            $contentLengths = explode(',', $headers->ContentLength ?? '');
            $contentLength = (int)array_pop($contentLengths);

            $exists = $headers && $contentLength > 0;
        } else {
            $exists = \Includes\Utils\FileManager::isFileReadable($path ?: $this->getStoragePath());
        }

        return $exists;
    }

    /**
     * Get URL
     *
     * @return string
     */
    public function getURL()
    {
        $url = null;

        if ($this->isURL()) {
            $url = $this->getPath();
        } elseif (static::STORAGE_RELATIVE == $this->getStorageType()) {
            $url = \XLite::getInstance()->getShopURL(
                $this->getWebRoot() . $this->convertPathToURL($this->getPath()),
                \XLite\Core\Request::getInstance()->isHTTPS()
            );
        } else {
            $root = $this->getFileSystemRoot();
            if (0 === strncmp($root, $this->getPath(), strlen($root))) {
                $path = substr($this->getPath(), strlen($root));
                $url = \XLite::getInstance()->getShopURL(
                    $this->getWebRoot() . $this->convertPathToURL($path),
                    \XLite\Core\Request::getInstance()->isHTTPS()
                );
            } else {
                $url = $this->getGetterURL();
            }
        }

        return $url;
    }

    /**
     * Get URL for customer front-end
     *
     * @return string
     */
    public function getFrontURL()
    {
        return \XLite\Core\Converter::makeURLValid($this->getURL());
    }

    /**
     * Get attachment getter URL
     *
     * @return string
     */
    public function getGetterURL()
    {
        return \XLite\Core\Converter::buildURL('storage', 'download', $this->getGetterParams(), \XLite::getCustomerScript());
    }

    /**
     * Get attachment getter URL
     *
     * @return string
     */
    public function getAdminGetterURL()
    {
        return \XLite\Core\Converter::buildURL('storage', 'download', $this->getGetterParams());
    }

    /**
     * Get file extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->isURL() ? $this->getExtensionByMIME() : pathinfo($this->getPath(), PATHINFO_EXTENSION);
    }

    /**
     * Get file extension by MIME type
     *
     * @return string
     */
    public function getExtensionByMIME()
    {
        if (isset(static::$types[$this->getMime()])) {
            $result = static::$types[$this->getMime()];

        } else {
            $result = '';
        }

        return $result;
    }

    /**
     * Check file is URL-based or not
     *
     * @param string $path Path OPTIONAL
     *
     * @return boolean
     */
    public function isURL($path = null)
    {
        return (bool)filter_var(!isset($path) ? $this->getPath() : $path, FILTER_VALIDATE_URL);
    }

    /**
     * Get MIME type icon URL
     *
     * @return string
     */
    public function getMimeClass()
    {
        return 'mime-icon-' . ($this->getExtension() ?: 'unknown');
    }

    /**
     * Get MIME type name
     *
     * @return string
     */
    public function getMimeName()
    {
        $ext = $this->getExtension();

        return $ext
            ? static::t('X file type', ['ext' => $ext])
            : '';
    }

    /**
     * Get load error code
     *
     * @return string
     */
    public function getLoadError()
    {
        return $this->loadError;
    }

    /**
     * Get load error message
     *
     * @return array
     */
    public function getLoadErrorMessage()
    {
        return $this->loadErrorMessage;
    }

    /**
     * Convert saved path to URL part
     *
     * @param string $path Path
     *
     * @return string
     */
    protected function convertPathToURL($path)
    {
        return implode('/', array_map('rawurlencode', explode('/', str_replace(LC_DS, '/', $path))));
    }

    /**
     * Get getter parameters
     *
     * @return array
     */
    protected function getGetterParams()
    {
        return [
            'storage' => get_called_class(),
            'id'      => $this->getId(),
        ];
    }

    // }}}

    // {{{ Loading

    /**
     * Load from request
     *
     * @param string $key Key in $_FILES service array
     *
     * @return boolean
     */
    public function loadFromRequest($key)
    {
        $path = \Includes\Utils\FileManager::moveUploadedFile($key, $this->getStoreFileSystemRoot());

        if ($path) {
            $this->setStorageType(static::STORAGE_RELATIVE);

            if (!empty($_FILES[$key]['type'])) {
                $this->setMime($_FILES[$key]['type']);
            }

            if (!$this->savePath($path)) {
                \Includes\Utils\FileManager::deleteFile($path);
                $path = null;
            } else {
                $this->setFileName(func_basename($this->getPath()));
            }

        } else {
            if (isset($_FILES[$key]['error']) && $error = $this->getUploadError($_FILES[$key]['error'])) {
                $this->loadErrorMessage = [$error];
            }

            \XLite\Logger::getInstance()->log('The file was not loaded', LOG_ERR);
        }

        return !empty($path);
    }

    protected function getUploadError($code)
    {
        switch($code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File uploading error 1';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File uploading error 2';
            case UPLOAD_ERR_PARTIAL:
                return 'File uploading error 3';
            case UPLOAD_ERR_NO_FILE:
                return 'File uploading error 4';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'File uploading error 6';
            case UPLOAD_ERR_CANT_WRITE:
                return 'File uploading error 7';
            case UPLOAD_ERR_EXTENSION:
                return 'File uploading error 8';
        }

        return null;
    }

    /**
     * Load from multiple request
     *
     * @param string  $key      Key in $_FILES service array
     * @param integer $position Position in multiple $_FILES service array
     *
     * @return boolean
     */
    public function loadFromMultipleRequest($key, $position)
    {
        $path = \Includes\Utils\FileManager::moveUploadedFileByMultiple($key, $position, $this->getStoreFileSystemRoot());

        if ($path) {
            $this->setStorageType(static::STORAGE_RELATIVE);

            if ('' != $_FILES[$key]['type'][$position]) {
                $this->setMime($_FILES[$key]['type'][$position]);
            }

            if (!$this->savePath($path)) {
                \Includes\Utils\FileManager::deleteFile($path);
                $path = null;
            }

        } else {
            \XLite\Logger::getInstance()->log('The file was not loaded', LOG_ERR);
        }

        return !empty($path);
    }

    /**
     * Load from local file
     *
     * @param string  $path       Absolute path
     * @param string  $basename   File name OPTIONAL
     * @param boolean $makeUnique True - create unique named file
     *
     * @return boolean
     */
    public function loadFromLocalFile($path, $basename = null, $makeUnique = false)
    {
        $result = true;
        $basename = $basename ?: basename($path);
        $relativePath = null;

        if (\Includes\Utils\FileManager::isExists($path) && $this->checkSecurity($path)) {

            if ($makeUnique) {
                $local = false;

            } else {
                foreach ($this->getAllowedFileSystemRoots() as $root) {
                    if ($relativePath = \Includes\Utils\FileManager::getRelativePath($path, $root)) {
                        $local = true;
                        break;
                    }
                }
            }

            if (empty($local)) {
                $newPath = \Includes\Utils\FileManager::getUniquePath(
                    $this->getStoreFileSystemRoot(),
                    $basename
                );

                $basename = basename($newPath);

                if (\Includes\Utils\FileManager::copy($path, $newPath)) {
                    $path = $newPath;
                    $this->setStorageType(static::STORAGE_RELATIVE);

                } else {

                    $this->loadError = 'unwriteable';
                    $this->loadErrorMessage = [
                        '{{file}} file could not be copied to a new location {{newPath}}',
                        ['file' => $path, 'newPath' => $newPath],
                    ];
                    \XLite\Logger::getInstance()->log(
                        '\'' . $path . '\' file could not be copied to a new location \'' . $newPath . '\'.',
                        LOG_ERR
                    );

                    $result = false;
                }

            } else {

                if ($relativePath && \Includes\Utils\FileManager::isReadable($path)) {
                    $path = $relativePath;
                    $this->setStorageType(static::STORAGE_RELATIVE);

                } else {
                    $this->setStorageType(static::STORAGE_ABSOLUTE);
                }
            }

        } else {
            $result = false;
        }

        if ($result && $basename) {
            $this->setFileName($basename);
        }

        return $result && $this->savePath($path, !empty($relativePath));
    }

    /**
     * Load from URL
     *
     * @param string  $url     URL
     * @param boolean $copy2fs Copy file to file system or not OPTIONAL
     *
     * @return boolean
     */
    public function loadFromURL($url, $copy2fs = false)
    {
        $remoteResource = $this->prepareURL($url);

        if ($remoteResource) {
            if ($copy2fs) {
                $result = $this->copyFromURL($remoteResource);
            } else {
                $name = $remoteResource->getName();
                $this->setPath($remoteResource->getURL());
                $this->setFileName($name);

                $result = $this->isFileExists($remoteResource->getURL());
            }

            return $result;
        } else {
            $this->loadErrorMessage = [
                'Unable to download the contents of {{url}}',
                ['url' => $url],
            ];
        }

        return false;
    }

    /**
     * @param string $url
     *
     * @return IURL
     */
    protected function prepareURL($url)
    {
        try {

            return RemoteResourceFactory::getRemoteResourceByURL($url);

        } catch (RemoteResourceException $e) {

            return null;
        }
    }

    /**
     * Copy file from URL
     *
     * @param IURL $remoteResource Remote resource
     *
     * @return boolean
     */
    protected function copyFromURL($remoteResource)
    {
        $result = false;

        $name = $remoteResource->getName();
        $url = $remoteResource->getURL();

        if ($remoteResource->isAvailable()) {
            try {
                $tmp = LC_DIR_TMP . $name;

                if (\XLite\Core\Operator::writeURLContentsToFile($url, $tmp)) {
                    $result = $this->loadFromLocalFile($tmp);
                    \Includes\Utils\FileManager::deleteFile($tmp);
                } else {
                    $this->loadError = 'unwriteable';
                    $this->loadErrorMessage = [
                        'Unable to write data to file {{file}}',
                        ['file' => $tmp],
                    ];

                    \XLite\Logger::getInstance()->log(
                        'Unable to write data to file \'' . $tmp . '\'.',
                        LOG_ERR
                    );
                }

            } catch (\Exception $e) {
                \Includes\Utils\FileManager::deleteFile($tmp);
                $this->loadError = 'undownloadable';
                $this->loadErrorMessage = [
                    'Unable to download the contents of {{url}}',
                    ['url' => $url],
                ];
                \XLite\Logger::getInstance()->log(
                    'Unable to download the contents of \'' . $url . '\'.',
                    LOG_ERR
                );
            }

        } else {
            $this->loadError = 'URLAvailability';
            $this->loadErrorMessage = [
                'Unable to get at the contents of {{url}}',
                ['url' => $url],
            ];
            \XLite\Logger::getInstance()->log(
                'Unable to get at the contents of \'' . $url . '\'.',
                LOG_ERR
            );
        }

        return $result;
    }

    /**
     * Is value local URL
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    public static function isValueLocalURL($value)
    {
        $domains = \XLite\Core\URLManager::getShopDomains();

        if (LC_DEVELOPER_MODE) {
            $domains[] = $_SERVER['HTTP_HOST'] ?? '';
        }

        $isSameHost = in_array(parse_url($value, PHP_URL_HOST), $domains, true);
        $path = static::getLocalPathFromURL($value);
        $isReadable = \Includes\Utils\FileManager::isFileReadable($path);
        return parse_url($value) && $isSameHost && $isReadable;
    }

    /**
     * Returns local part of path from local URL
     *
     * @return string
     */
    public static function getLocalPathFromURL($path)
    {
        $webDir = ltrim(\XLite::getInstance()->getOptions(['host_details', 'web_dir']) . '/', '/');

        $localPath = preg_replace(
            '#^' . preg_quote($webDir) . '#',
            '',
            ltrim(parse_url($path, PHP_URL_PATH), '/')
        );

        return LC_DIR_ROOT . str_replace('/', LC_DS, $localPath);
    }

    /**
     * Load from path
     *
     * @param      $path
     * @param bool $copy2fs
     *
     * @return bool
     */
    public function loadFromPath($path, $copy2fs = true)
    {
        $result = false;

        if (!filter_var($path, FILTER_VALIDATE_URL)) {
            $filePath = static::isValueLocalURL($path)
                ? static::getLocalPathFromURL($path)
                : $path;

            $result = $this->loadFromLocalFile(LC_DIR_ROOT . $filePath);
        }

        if (!$result) {
            $result = $this->loadFromURL($path, $copy2fs);
        }

        if (!$result && !$this->loadError) {
            $this->loadError = 'nonexistent';
            $this->loadErrorMessage = [
                '{{file}} does not exist on the filesystem',
                ['file' => $path],
            ];
        }

        return $result;
    }

    // }}}

    // {{{ Service operations

    /**
     * Remove file
     *
     * @param string $path Path OPTIONAL
     *
     * @return void
     */
    public function removeFile($path = null)
    {
        $path = $this->getStoragePath($path);

        if ($this->isAllowRemoveFile($path)) {
            \Includes\Utils\FileManager::deleteFile($path);
        }
    }

    /**
     * Is allow to remove file
     *
     * @param null $path
     *
     * @return bool
     */
    public function isAllowRemoveFile($path = null)
    {
        $path = $this->getStoragePath($path);

        return !$this->isURL($path)
            && $this->getStorageType() !== static::STORAGE_ABSOLUTE
            && $this->getRepository()->allowRemovePath($path, $this);
    }

    /**
     * Renew storage
     *
     * @return boolean
     */
    public function renewStorage()
    {
        $result = $this->renew();

        foreach ($this->getDuplicates() as $storage) {
            $result = $result && $storage->renewDependentStorage();
        }

        return $result;
    }

    /**
     * Renew dependent storage
     *
     * @return boolean
     */
    public function renewDependentStorage()
    {
        return $this->renew();
    }

    /**
     * Get duplicates storages
     *
     * @return array
     */
    public function getDuplicates()
    {
        return $this->getRepository()->findByFullPath($this->getStoragePath() ?: $this->getPath(), $this);
    }

    /**
     * Prepare order before save data operation
     *
     * @return void
     *
     * @PrePersist
     * @PreUpdate
     */
    public function prepareBeforeSave()
    {
        if (!$this->getDate()) {
            $this->setDate(\XLite\Core\Converter::time());
        }
    }

    /**
     * Prepare order before save data operation
     *
     * @return void
     *
     * @PreRemove
     */
    public function prepareRemove()
    {
        if (!$this->isURL()) {
            $this->removeFile();
        }
    }

    /**
     * Get storage path
     *
     * @param string $path Path to use OPTIONAL
     *
     * @return string
     */
    public function getStoragePath($path = null)
    {
        $result = null;

        if (static::STORAGE_RELATIVE == $this->getStorageType()) {
            if ($path && strpos($path, $this->getFileSystemRoot()) === 0) {
                $result = $path;
            } else {
                $result = $this->getFileSystemRoot() . ($path ?: $this->getPath());
            }
        } elseif (static::STORAGE_ABSOLUTE == $this->getStorageType()) {
            $result = ($path ?: $this->getPath());
        } elseif (static::STORAGE_URL == $this->getStorageType()){
            $result = $this->getPath();
        }

        return $result;
    }

    /**
     * Save path into entity
     *
     * @param string  $path     Full path
     * @param boolean $pathAsIs Accept $path as is (do not use assembleSavePath() method) OPTIONAL
     *
     * @return boolean
     */
    protected function savePath($path, $pathAsIs = false)
    {
        $this->loadError = null;

        // Remove old file
        $savePath = $pathAsIs || static::STORAGE_ABSOLUTE == $this->getStorageType()
            ? $path
            : $this->assembleSavePath($path);
        $toRemove = $this->getPath() && $this->getPath() !== $savePath;

        $pathToRemove = $this->getPath();
        $this->setPath($savePath);
        if (!$this->getFileName()) {
            $this->setFileName(func_basename($this->getPath()));
        }

        $result = $this->renew() && $this->updatePathByMIME();
        $secure = $this->checkSecurity();
        $result = $result && $secure;

        if ($result && $toRemove) {
            $this->removeFile($pathToRemove);
        } elseif (!$secure) {
            $this->removeFile($this->getPath());
        }

        return $result;
    }

    /**
     * Assemble path for save into DB
     *
     * @param string $path Path
     *
     * @return string
     */
    protected function assembleSavePath($path)
    {
        return func_basename($path);
    }

    /**
     * Update file path - change file extension taken from MIME information.
     *
     * @return boolean
     */
    protected function updatePathByMIME()
    {
        return true;
    }

    /**
     * Renew parameters
     *
     * @return boolean
     */
    protected function renew()
    {
        list($path, $isTempFile) = $this->getLocalPath();

        $result = $this->isFileExists($path, $isTempFile) && $this->renewByPath($path);

        if ($isTempFile || (!$result && !$this->isURL($path))) {
            \Includes\Utils\FileManager::deleteFile($path);
        }

        return $result;
    }

    /**
     * Renew properties by path
     *
     * @param string $path Path
     *
     * @return boolean
     */
    protected function renewByPath($path)
    {
        $this->setSize((int)\Includes\Utils\FileManager::getFileSize($path));
        $this->setDate(\XLite\Core\Converter::time());

        return true;
    }

    /**
     * Check storage security
     *
     * @since 5.3.3.4 Added $path param to allow check not uploaded files
     *
     * @param string $path File path to check (OPTIONAL)
     *
     * @return bool
     */
    protected function checkSecurity($path = null)
    {
        $path = $path ?: $this->getPath();
        return $this->checkPathExtension($path);
    }

    /**
     * Check path extension
     *
     * @since 5.3.3.4 Added $path param to allow check not uploaded files
     *
     * @param string $path File path to check (OPTIONAL)
     *
     * @return bool
     */
    protected function checkPathExtension($path)
    {
        $path = $path ?: $this->getPath();
        $result = true;

        if (preg_match('/\.(?:php3?|pl|cgi|py|htaccess|phtml)$/Ss', $path)) {
            $this->loadError = 'extension';
            $this->loadErrorMessage = [
                'The file extension is forbidden ({{file}})',
                ['file' => $path],
            ];

            $result = false;
        }

        return $result;
    }

    /**
     * Get local path for file-based PHP functions
     *
     * @return string
     */
    protected function getLocalPath()
    {
        $isTempFile = false;

        if ($this->isURL()) {
            $path = tempnam(LC_DIR_TMP, 'analyse_file');
            if (!\Includes\Utils\FileManager::write($path, $this->getBody())) {
                \XLite\Logger::getInstance()->log(
                    'Unable to write data to file \'' . $path . '\'.',
                    LOG_ERR
                );
                $path = false;
            }
            $isTempFile = true;

        } else {
            $path = $this->getStoragePath();
        }

        return [$path, $isTempFile];
    }

    /**
     * Get allowed file system root list
     *
     * @return array
     */
    protected function getAllowedFileSystemRoots()
    {
        return $this->getRepository()->getAllowedFileSystemRoots();
    }

    /**
     * Get valid file system storage root
     *
     * @return string
     */
    protected function getValidFileSystemRoot()
    {
        $path = $this->getFileSystemRoot();
        \Includes\Utils\FileManager::mkdirRecursive($path);

        return $path;
    }

    /**
     * Get valid file system storage root
     *
     * @return string
     */
    protected function getStoreFileSystemRoot()
    {
        return $this->getValidFileSystemRoot();
    }

    /**
     * Get file system images storage root path
     *
     * @return string
     */
    protected function getFileSystemRoot()
    {
        return $this->getRepository()->getFileSystemRoot();
    }

    /**
     * Get web images storage root path
     *
     * @return string
     */
    protected function getWebRoot()
    {
        return $this->getRepository()->getWebRoot();
    }

    // }}}

    /**
     * Check file is an image or not
     *
     * @return boolean
     */
    public function isImage()
    {
        return false;
    }

    /**
     * Get list of administrator permissions to download files of the storage
     *
     * @return array
     */
    public function getAdminPermissions()
    {
        return [];
    }
}
