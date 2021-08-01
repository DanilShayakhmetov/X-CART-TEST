<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Logic\BulkEdit\Field\Product;

/**
 * @Decorator\Depend ("XC\BulkEditing")
 */
abstract class QuantityInStock extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\QuantityInStock implements \XLite\Base\IDecorator
{
    public static function populateData($name, $object, $data)
    {
        if (!$object->hasManualPinCodes()) {
            parent::populateData($name, $object, $data);
        }
    }
}
