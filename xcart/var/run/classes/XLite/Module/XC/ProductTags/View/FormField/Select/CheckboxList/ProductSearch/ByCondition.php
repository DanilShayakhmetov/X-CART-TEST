<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\View\FormField\Select\CheckboxList\ProductSearch;

use XLite\Model\Repo\Product;

 class ByCondition extends \XLite\View\FormField\Select\CheckboxList\ProductSearch\ByConditionAbstract implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array_merge(
            parent::getDefaultOptions(),
            [Product::P_BY_TAG => static::t('Tag')]
        );
    }
}
