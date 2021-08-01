<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\FormField\Select;

/**
 * Rating selection widget
 */
class ReviewRating extends \XLite\View\FormField\Select\CheckboxList\ACheckboxList
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            5 => \XLite\Core\Translation::lbl('X stars_5', ['count' => 5]),
            4 => \XLite\Core\Translation::lbl('X stars_4', ['count' => 4]),
            3 => \XLite\Core\Translation::lbl('X stars_3', ['count' => 3]),
            2 => \XLite\Core\Translation::lbl('X stars_2', ['count' => 2]),
            1 => \XLite\Core\Translation::lbl('X star_1', ['count' => 1]),
        ];
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $list = parent::setCommonAttributes($attrs);
        $list['data-placeholder'] = static::t('Any rating');

        return $list;
    }
}
