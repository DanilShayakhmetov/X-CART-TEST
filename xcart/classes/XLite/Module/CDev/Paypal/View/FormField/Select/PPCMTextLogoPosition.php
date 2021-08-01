<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\FormField\Select;

class PPCMTextLogoPosition extends \XLite\View\FormField\Select\Regular
{
    const PPCM_LEFT  = 'left';
    const PPCM_RIGHT = 'right';
    const PPCM_TOP   = 'top';

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::PPCM_LEFT  => static::t('Left (ppcm)'),
            static::PPCM_RIGHT => static::t('Right (ppcm)'),
            static::PPCM_TOP   => static::t('Top (ppcm)'),
        ];
    }
}
