<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\FormField\Select;

/**
 * Auto pin codes selector 
 *
 */
class AutoPinCodes extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            true  => static::t('Automatically'),
            false => static::t('Manually')
        ];
    }

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return intval(parent::getValue());
    }
}
