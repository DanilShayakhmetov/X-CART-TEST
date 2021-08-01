<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;


class RoundUp extends \XLite\View\FormField\Select\Regular
{
    /**
     * @inheritdoc
     */
    protected function getDefaultOptions()
    {
        return [
            'N' => static::t('No roundup'),
            2   => static::t('up to 2 decimals'),
            1   => static::t('up to 1 decimal'),
            0   => static::t('up to integer'),
            -1  => static::t('up to 10'),
            -2  => static::t('up to 100'),
        ];
    }
}