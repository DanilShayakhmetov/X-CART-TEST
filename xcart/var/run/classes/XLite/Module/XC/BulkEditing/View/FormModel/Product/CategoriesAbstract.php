<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\FormModel\Product;

abstract class CategoriesAbstract extends AProduct
{
    public function __construct(array $params)
    {
        $this->scenario = 'product_categories';

        parent::__construct($params);
    }
}
