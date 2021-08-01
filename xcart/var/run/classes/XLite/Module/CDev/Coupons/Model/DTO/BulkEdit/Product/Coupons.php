<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Model\DTO\BulkEdit\Product;

/**
 * @Decorator\Depend ("XC\BulkEditing")
 */
class Coupons extends \XLite\Module\XC\BulkEditing\Model\DTO\Product\AProduct
{
    public function __construct($data = null)
    {
        $this->scenario = 'coupons';

        parent::__construct($data);
    }
}
