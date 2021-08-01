<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\FormModel\Product\Attribute;

class HiddenAttribute extends \XLite\Module\XC\BulkEditing\View\FormModel\Product\Attribute\AAttribute
{
    public function __construct(array $params)
    {
        $this->scenario = 'product_hidden_attributes';

        parent::__construct($params);
    }
}
