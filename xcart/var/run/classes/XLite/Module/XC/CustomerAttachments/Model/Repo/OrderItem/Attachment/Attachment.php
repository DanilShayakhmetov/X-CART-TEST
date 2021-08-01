<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomerAttachments\Model\Repo\OrderItem\Attachment;

/**
 * @Api\Operation\Create(modelClass="XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment", summary="Add customer attachment")
 * @Api\Operation\Read(modelClass="XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment", summary="Retrieve customer attachment by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment", summary="Retrieve customer attachments by conditions")
 * @Api\Operation\Update(modelClass="XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment", summary="Update customer attachment by id")
 * @Api\Operation\Delete(modelClass="XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment", summary="Delete customer attachment by id")
 *
 * @SWG\Tag(
 *   name="XC\CustomerAttachments\OrderItem\Attachment\Attachment",
 *   x={"display-name": "OrderItem\Attachment\Attachment", "group": "XC\CustomerAttachments"},
 *   description="This repo stores user-created global product tabs.",
 * )
 */
class Attachment extends \XLite\Model\Repo\Base\Storage
{
    /**
     * Get storage name
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'customer_attachments';
    }

    /**
     * Get file system images storage root path
     *
     * @return string
     */
    public function getFileSystemRoot()
    {
        return LC_DIR_FILES . $this->getStorageName() . LC_DS;
    }

    /**
     * Get web images storage root path
     *
     * @return string
     */
    public function getWebRoot()
    {
        return LC_FILES_URL . '/' . $this->getStorageName() . '/';
    }
}
