<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\Model\Product\Attachment;

/**
 * Product attchament's storage 
 *
 *  Entity
 *  Table  (name="product_attachment_storages")
 */
abstract class StorageAbstract extends \XLite\Model\Base\Storage
{
    // {{{ Associations

    /**
     * Relation to a attachment
     *
     * @var \XLite\Module\CDev\FileAttachments\Model\Product\Attachment
     *
     * @OneToOne  (targetEntity="XLite\Module\CDev\FileAttachments\Model\Product\Attachment", inversedBy="storage")
     * @JoinColumn (name="attachment_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attachment;

    // }}}

    // {{{ Service operations

    /**
     * Get valid file system storage root
     *
     * @return string
     */
    protected function getValidFileSystemRoot()
    {
        $path = parent::getValidFileSystemRoot();

        if (!file_exists($path . LC_DS . '.htaccess')) {
            $contents = <<<HTACCESS
Options -Indexes

<Files "*.php">
  Deny from all
</Files>

<Files "*.php3">
  Deny from all
</Files>

<Files "*.pl">
  Deny from all
</Files>

<Files "*.py">
  Deny from all
</Files>

Allow from all
HTACCESS;

            file_put_contents(
                $path . LC_DS . '.htaccess',
                $contents
            );
        }

        return $path;
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
        return $this->getAttachment()->getProduct()->getProductId() . LC_DS . parent::assembleSavePath($path);
    }

    /**
     * Get valid file system storage root
     *
     * @return string
     */
    protected function getStoreFileSystemRoot()
    {
        $path = parent::getStoreFileSystemRoot() . $this->getAttachment()->getProduct()->getProductId() . LC_DS;
        \Includes\Utils\FileManager::mkdirRecursive($path);

        return $path;
    }

    /**
     * Clone for attachment
     *
     * @param \XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment Attachment
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntityForAttachment(\XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment)
    {
        $newStorage = parent::cloneEntity();

        $attachment->setStorage($newStorage);
        $newStorage->setAttachment($attachment);

        if (static::STORAGE_URL !== $this->getStorageType()) {
            // Clone local image (will be created new file with unique name)
            $newStorage->loadFromLocalFile($this->getStoragePath(), null, false);
        }

        return $newStorage;
    }

    /**
     * Get list of administrator permissions to download files of the storage
     *
     * @return array
     */
    public function getAdminPermissions()
    {
        return ['manage catalog'];
    }

    /**
     * Set mime
     *
     * @param string $mime
     * @return Storage
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
     * @return Storage
     */
    public function setStorageType($storageType)
    {
        $this->storageType = $storageType;
        return $this;
    }

    /**
     * Set attachment
     *
     * @param \XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment
     * @return Storage
     */
    public function setAttachment(\XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment = null)
    {
        $this->attachment = $attachment;
        return $this;
    }

    /**
     * Get attachment
     *
     * @return \XLite\Module\CDev\FileAttachments\Model\Product\Attachment 
     */
    public function getAttachment()
    {
        return $this->attachment;
    }
}

