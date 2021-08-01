<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Model\Product\Attachment;

use XLite\Core\Config;
use XLite\Core\RemoteResource\IURL;
use XLite\Module\CDev\Egoods\Core\AmazonS3;

/**
 * Storage
 *
 * @MappedSuperclass
 */
abstract class Storage extends \XLite\Module\CDev\FileAttachments\Model\Product\Attachment\StorageAbstract implements \XLite\Base\IDecorator
{
    /**
     * Private suffix length
     */
    const PRIVATE_SUFFIX_LENGTH = 33;

    /**
     * Get URL
     *
     * @return string
     */
    public function getURL()
    {
        return $this->getAttachment()->getPrivate() ? $this->getGetterURL() : parent::getURL();
    }

    /**
     * Get URL for customer front-end
     *
     * @return string
     */
    public function getFrontURL()
    {
        return $this->getAttachment()->getPrivate() ? null : parent::getFrontURL();
    }

    /**
     * Get file extension
     *
     * @return string
     */
    public function getExtension()
    {
        $ext = null;
        if ($this->getAttachment()->getPrivate() && !$this->isURL()) {
            $ext = explode('.', pathinfo($this->getPath(), PATHINFO_FILENAME));
            $ext = $ext[count($ext) - 1];
        }

        return $ext ?: parent::getExtension();
    }

    /**
     * Get download URL for customer front-end by key
     *
     * @return string
     */
    public function getDownloadURL(\XLite\Module\CDev\Egoods\Model\OrderItem\PrivateAttachment $attachment)
    {
        $params = $this->getGetterParams();
        $params['key'] = $attachment->getDownloadKey();

        return \XLite\Core\Converter::buildFullURL('storage', 'download', $params, \XLite::getCustomerScript());
    }

    /**
     * Mask storage
     *
     * @return void
     */
    public function maskStorage()
    {
        if ($this->getPath()) {
            $path = $this->getStoragePath();
            $suffix = md5(strval(microtime(true)) . strval(mt_rand(0, 1000000)));
            rename($path, $path . '.' . $suffix);
            $this->setPath($this->getPath() . '.' . $suffix);
        }
    }

    /**
     * Unmask storage
     *
     * @return void
     */
    public function unmaskStorage()
    {
        if ($this->getPath()) {
            $path = $this->getStoragePath();
            rename($path, substr($path, 0, static::PRIVATE_SUFFIX_LENGTH * -1));
            $this->setPath(substr($this->getPath(), 0, static::PRIVATE_SUFFIX_LENGTH * -1));
        }
    }

    /**
     * Check - path ir private or not
     *
     * @param string $path Path OPTIONAL
     *
     * @return boolean
     */
    public function isPrivatePath($path = null)
    {
        $path = $path ?: $this->getPath();

        return (bool)preg_match('/\.[a-f0-9]{32}$/Ss', $path);
    }

    /**
     * Check if the attachment url can be signed to protect from unauthorized access
     *
     * @param string $path Path OPTIONAL
     *
     * @return boolean
     */
    public function canBeSigned($path = null)
    {
        $config = Config::getInstance()->CDev->Egoods;
        if ($config->enable_signed_urls) {
            if (
                ($config->storage_type === 'as3' && preg_match('/\/\/.*?s3.*?amazonaws\.com/', $path ?? $this->getPath()))
                || ($config->storage_type === 'dos' && preg_match('/\/\/.*?.digitaloceanspaces\.com/', $path ?? $this->getPath()))
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns signed url to access Amazon S3 content
     * @return string
     */
    public function getSignedUrl()
    {
        if ($this->canBeSigned()) {
            return $this->getCloudPath();
        }

        return $this->getURL();
    }

    /**
     * @param string $path Path OPTIONAL
     *
     * @return string
     */
    protected function getCloudPath($path = null)
    {
        $config = Config::getInstance()->CDev->Egoods;

        if ($config->storage_type === 'as3') {
            preg_match('/\/\/.*?s3.*?amazonaws\.com\/(.*)/', $path ?? $this->getPath(), $matches);
        }
        if ($config->storage_type === 'dos') {
            preg_match('/\/\/.*?.digitaloceanspaces\.com\/(.*)/', $path ?? $this->getPath(), $matches);
        }

        return !empty($matches[1]) ? AmazonS3::getInstance()->getPresignedUrl($matches[1]) : '';
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
        return $this->canBeSigned() ? true : parent::isFileExists($path, $forceFile);
    }

    /**
     * @param string  $path      Path to check OPTIONAL
     * @param boolean $forceFile Flag OPTIONAL
     *
     * @return boolean
     */
    public function isFileAvailable($path = null, $forceFile = false)
    {
        return parent::isFileExists($path, $forceFile);
    }

    /**
     * @inheritdoc
     */
    public function loadFromURL($url, $copy2fs = false)
    {
        if (!$copy2fs && $this->getAttachment() && $this->getAttachment()->getPrivate() && !$this->canBeSigned()) {
            $this->loadError = 'public_url';
            $this->loadErrorMessage = ['File is available by public URL'];
            return false;
        }

        return parent::loadFromURL($url, $copy2fs);
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
        $remoteUrl = $remoteResource->getURL();
        if ($this->canBeSigned($remoteUrl) && !$this->isFileAvailable($remoteUrl)) {
            $remoteResource->setUrl($this->getCloudPath($remoteUrl));
        }

        return parent::copyFromURL($remoteResource);
    }
}

