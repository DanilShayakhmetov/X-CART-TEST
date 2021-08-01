<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Product;

/**
 * Attributes
 */
class Attributes extends \XLite\View\StickyPanel\Product\AProduct
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = [];
        
        if ('global' === \XLite\Core\Request::getInstance()->spage) {
            $list['saveMode'] = $this->getWidget(
                [
                    'fieldName'  => 'save_mode',
                    'attributes' => [
                        'class' => 'not-significant',
                    ],
                    'disabled'   => true,
                    'value'      => false,
                    'label'      => static::t("Apply attribute value changes for all the products")
                ],
                'XLite\View\FormField\Input\Checkbox\OnOff'
            );
        }

        return array_merge(parent::defineButtons(), $list);
    }
}
