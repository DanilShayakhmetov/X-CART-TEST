<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Image\Category;

/**
 * Category
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Image\Category\Image", summary="Add new category thumbnail")
 * @Api\Operation\Read(modelClass="XLite\Model\Image\Category\Image", summary="Retrieve category thumbnail by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Image\Category\Image", summary="Retrieve all category thumbnails")
 * @Api\Operation\Update(modelClass="XLite\Model\Image\Category\Image", summary="Update category thumbnail by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Image\Category\Image", summary="Delete category thumbnail by id")
 */
class Image extends \XLite\Model\Repo\Base\Image
{
    /**
     * Returns the name of the directory within 'root/images' where images stored
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'category';
    }
}
