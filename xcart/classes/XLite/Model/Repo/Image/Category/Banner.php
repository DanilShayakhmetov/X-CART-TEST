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
 * @Api\Operation\Create(modelClass="XLite\Model\Image\Category\Banner", summary="Add new category banner image")
 * @Api\Operation\Read(modelClass="XLite\Model\Image\Category\Banner", summary="Retrieve category banner image by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Image\Category\Banner", summary="Retrieve all category banner images")
 * @Api\Operation\Update(modelClass="XLite\Model\Image\Category\Banner", summary="Update category banner image by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Image\Category\Banner", summary="Delete category banner image by id")
 */
class Banner extends \XLite\Model\Repo\Base\Image
{
    /**
     * Returns the name of the directory within 'root/images' where images stored
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'category_banner';
    }
}
