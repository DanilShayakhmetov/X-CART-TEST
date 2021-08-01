<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\FormField\Select;

class Revenue extends \XLite\View\FormField\Select\Regular
{
    protected function getDefaultOptions()
    {
        return [
            ''         => static::t('Select'),
            '50k'      => static::t('Just starting out: less than $50k revenue'),
            '50k-250k' => static::t('Building a business: $50k - $250k revenue'),
            '250k-1m'  => static::t('Growing business: $250k - $1M revenue'),
            '1m-20m'   => static::t('Maturing business: $1M - $20M revenue'),
            '20m-100m' => static::t('Established business: $20M - $100M revenue'),
            'more100m' => static::t('Enterprise: more than $100M revenue'),
            'not_sure' => static::t('I\'m not sure'),
        ];
    }
}