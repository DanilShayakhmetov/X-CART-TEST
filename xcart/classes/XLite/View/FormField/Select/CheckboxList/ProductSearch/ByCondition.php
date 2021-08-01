<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\CheckboxList\ProductSearch;

use XLite\Model\Repo\Product;
use XLite\View\FormField\Select\CheckboxList\Simple;

class ByCondition extends Simple
{
    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            Product::P_BY_TITLE => static::t('Name'),
            Product::P_BY_SKU   => static::t('SKU'),
            Product::P_BY_DESCR => static::t('Description'),
        ];
    }
}
