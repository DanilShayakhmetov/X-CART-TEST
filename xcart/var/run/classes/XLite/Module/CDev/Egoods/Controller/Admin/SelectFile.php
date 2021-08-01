<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Controller\Admin;

/**
 * @Decorator\After("CDev\FileAttachments")
 */
 class SelectFile extends \XLite\Module\CDev\FileAttachments\Controller\Admin\SelectFile implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Model\Base\Storage $storage
     *
     * @return string
     */
    protected function getAttachmentHash(\XLite\Model\Base\Storage $storage)
    {
        if (\XLite\Model\Base\Storage::STORAGE_URL === $storage->getStorageType()) {
            return \Includes\Utils\FileManager::getHash($storage->getSignedUrl(), true);
        }

        return parent::getAttachmentHash($storage);
    }
}
