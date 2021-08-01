<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Logic\Feed\Step;

use Includes\Utils\URLManager;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Router;

/**
 * Products step
 *
 * @Decorator\Depend("XC\MultiVendor")
 */
class ProductsMultiVendor extends \XLite\Module\XC\GoogleFeed\Logic\Feed\Step\Products implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Module\XC\MultiVendor\Model\Product $model
     * @return string
     */
    protected function getRecordId(\XLite\Model\Product $model)
    {
        return $model->getVendor()
            ? $model->getVendorId() . '-' . parent::getRecordId($model)
            : parent::getRecordId($model);
    }
}