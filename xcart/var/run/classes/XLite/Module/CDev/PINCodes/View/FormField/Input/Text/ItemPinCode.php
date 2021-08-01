<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\FormField\Input\Text;


class ItemPinCode extends \XLite\View\FormField\Input\Text
{
    protected function assembleClasses(array $classes)
    {
        return array_merge(parent::assembleClasses($classes), [
            'not-affect-recalculate',
        ]);
    }

    protected function getDefaultPlaceholder()
    {
        return static::t('PIN code');
    }
}