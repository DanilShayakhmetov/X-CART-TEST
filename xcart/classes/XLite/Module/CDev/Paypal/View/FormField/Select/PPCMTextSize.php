<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\FormField\Select;

class PPCMTextSize extends \XLite\View\FormField\Select\Regular
{
    const PPCM_SMALL  = '10';
    const PPCM_MEDIUM = '12';
    const PPCM_LARGE  = '15';

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::PPCM_SMALL  => static::t('Small (ppcm)'),
            static::PPCM_MEDIUM => static::t('Medium (ppcm)'),
            static::PPCM_LARGE  => static::t('Large (ppcm)'),
        ];
    }
}
