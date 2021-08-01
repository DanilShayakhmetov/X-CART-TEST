<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\ItemsList\BulkEdit\Product;

/**
 * Abstract product list
 */
class Category extends \XLite\Module\XC\BulkEditing\View\ItemsList\BulkEdit\AProduct
{
    public function __construct(array $params)
    {
        $this->scenario = 'product_categories';

        parent::__construct($params);
    }

    /**
     * Preprocess categories
     *
     * @param string               $value  Value
     * @param array                $column Column data
     * @param \XLite\Model\Product $entity Product
     *
     * @return string
     */
    protected function preprocessCategories($value, array $column, \XLite\Model\Product $entity)
    {
        return func_htmlspecialchars($value);
    }
}
