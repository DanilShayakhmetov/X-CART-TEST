<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Model\Product;

/**
 * Product attachment
 *
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 */
abstract class Attachment extends \XLite\Module\CDev\FileAttachments\Model\Product\AttachmentAbstract implements \XLite\Base\IDecorator
{
    /**
     * Private attachment
     *
     * @var   boolean
     *
     * @Column (type="boolean")
     */
    protected $private = false;

    /**
     * Old scope 
     * 
     * @var   boolean
     */
    protected $oldScope;

    /**
     * Attachment history
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\CDev\Egoods\Model\Product\Attachment\AttachmentHistoryPoint", mappedBy="attachment", cascade={"all"})
     * @OrderBy   ({"date" = "ASC"})
     */
    protected $history;

    /**
     * Return Private
     *
     * @return boolean
     */
    public function getPrivate()
    {
        return $this->private && $this->canBePrivate();
    }

    /**
     * Set private scope flag
     * 
     * @param boolean $private Scope flag
     *  
     * @return void
     */
    public function setPrivate($private)
    {
        if (!isset($this->oldScope)) {
            $this->oldScope = $this->private;
        }

        $this->private = intval($private);

        $this->prepareChangeScope();
    }

    /**
     * Checks if this attachment can be private in current store conditions
     */
    public function canBePrivate()
    {
        return $this->getStorage() && (!$this->getStorage()->isURL() || $this->getStorage()->canBeSigned());
    }

    /**
     * Set private flag for duplicate attachment
     * 
     * @param boolean                                                             $private Private flag
     * @param \XLite\Module\CDev\FileAttachments\Model\Product\Attachment\Storage $storage Original storage
     *  
     * @return void
     */
    public function setDuplicatePrivate($private, \XLite\Module\CDev\FileAttachments\Model\Product\Attachment\Storage $storage)
    {
        $this->getStorage()->setPath($storage->getPath());
        $this->getStorage()->setStorageType($storage->getStorageType());
        $this->private = $private;
        $this->oldScope = $private;
    }

    /**
     * Prepare change scope 
     *
     * @return void
     */
    public function prepareChangeScope()
    {
        $storage = $this->getStorage();

        if (!$storage->isURL() && isset($this->oldScope) && $this->oldScope != $this->getPrivate()) {
            $duplicates = $this->getStorage()->getDuplicates();

            if ($this->getPrivate()) {
                $storage->maskStorage();

            } else {

                if ($storage->isPrivatePath()) {
                    $storage->unmaskStorage();
                }
            }

            foreach ($duplicates as $duplicate) {
                if ($duplicate instanceof \XLite\Module\CDev\FileAttachments\Model\Product\Attachment\Storage) {
                    $duplicate->getAttachment()->setDuplicatePrivate($this->getPrivate(), $this->getStorage());
                }
            }

            $this->oldScope = $this->getPrivate();
        }
    }

    /**
     * Synchronize private state 
     * 
     * @return void
     *
     * @PrePersist
     */
    public function synchronizePrivateState()
    {
        if ($this->getStorage()->isPrivatePath()) {
            $this->oldScope = true;
            $this->setPrivate(true);
            $this->getStorage()->setFileName(
                substr(
                    $this->getStorage()->getFileName(),
                    0,
                    \XLite\Module\CDev\Egoods\Model\Product\Attachment\Storage::PRIVATE_SUFFIX_LENGTH * -1
                )
            );
        }
    }

    /**
     * Return History
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Set History
     *
     * @param \XLite\Module\CDev\Egoods\Model\Product\Attachment\AttachmentHistoryPoint $attachmentHistoryPoint
     *
     * @return $this
     */
    public function addHistoryPoint($attachmentHistoryPoint)
    {
        $this->history[] = $attachmentHistoryPoint;
        return $this;
    }

    /**
     * Get attachment icon type
     *
     * @return string
     */
    public function getIconType()
    {
        /** @var \XLite\Module\CDev\Egoods\Model\Product\Attachment\Storage $storage */
        $storage = $this->getStorage();

        if ($storage && $storage->canBeSigned()) {
            return 's3';
        }

        return parent::getIconType();
    }
}
