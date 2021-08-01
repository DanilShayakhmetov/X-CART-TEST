<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Image;

/**
 * Content image repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Image\Content", summary="Add new content image")
 * @Api\Operation\Read(modelClass="XLite\Model\Image\Content", summary="Retrieve content image by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Image\Content", summary="Retrieve all content images")
 * @Api\Operation\Update(modelClass="XLite\Model\Image\Content", summary="Update content image by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Image\Content", summary="Delete content image by id")
 */
class Content extends \XLite\Model\Repo\Base\Image
{
    /**
     * Returns the name of the directory within 'root/images' where images stored
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'content';
    }
}
