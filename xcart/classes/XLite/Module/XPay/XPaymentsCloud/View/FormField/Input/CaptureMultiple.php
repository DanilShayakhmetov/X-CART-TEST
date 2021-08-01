<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\FormField\Input;


class CaptureMultiple extends \XLite\View\FormField\Input\RefundMultiple
{

    /**
     * getLabel
     *
     * @return string
     */
    public function getLabel()
    {
        return static::t('Capture');
    }

}
