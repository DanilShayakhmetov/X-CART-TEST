<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Image;

/**
 * Product varioant's image
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Image\BannerRotationImage", summary="Add new banner image")
 * @Api\Operation\Read(modelClass="XLite\Model\Image\BannerRotationImage", summary="Retrieve banner image by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Image\BannerRotationImage", summary="Retrieve all banner images")
 * @Api\Operation\Update(modelClass="XLite\Model\Image\BannerRotationImage", summary="Update banner image by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Image\BannerRotationImage", summary="Delete banner image by id")
 */
class BannerRotationImage extends \XLite\Model\Repo\Base\Image
{
    /**
     * Returns the name of the directory within 'root/images' where images stored
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'banner_rotation';
    }
}
