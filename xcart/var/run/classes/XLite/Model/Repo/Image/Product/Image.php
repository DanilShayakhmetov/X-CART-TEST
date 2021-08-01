<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Image\Product;

/**
 * Product image
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Image\Product\Image", summary="Add new product image")
 * @Api\Operation\Read(modelClass="XLite\Model\Image\Product\Image", summary="Retrieve product image by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Image\Product\Image", summary="Retrieve all product images")
 * @Api\Operation\Update(modelClass="XLite\Model\Image\Product\Image", summary="Update product image by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Image\Product\Image", summary="Delete product image by id")
 */
class Image extends \XLite\Model\Repo\Base\Image
{
    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'orderby';

    /**
     * Returns the name of the directory within 'root/images' where images stored
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'product';
    }

}
