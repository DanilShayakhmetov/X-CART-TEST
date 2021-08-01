<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\FormField\Select;

/**
 * Menu selector
 */
class ShowLinksInCategoryMenu extends \XLite\View\FormField\Select\Regular
{
    const TYPE_NOT_DISPLAY = 'not_display';
    const TYPE_UNDER_CATEGORIES = 'under_categories';
    const TYPE_ABOVE_CATEGORIES = 'above_categories';

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            static::TYPE_NOT_DISPLAY => static::t('Do not display'),
            static::TYPE_UNDER_CATEGORIES => static::t('Display under categories list'),
            static::TYPE_ABOVE_CATEGORIES => static::t('Display above categories list'),
        );
    }
}
