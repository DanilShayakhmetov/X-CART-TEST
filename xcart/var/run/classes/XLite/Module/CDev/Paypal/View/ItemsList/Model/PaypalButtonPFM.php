<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\ItemsList\Model;

class PaypalButtonPFM extends \XLite\Module\CDev\Paypal\View\ItemsList\Model\PaypalButton
{
    /**
     * Get plain data
     *
     * @return array
     */
    protected function getPlainData()
    {
        $data = parent::getPlainData();

        return [
            static::TYPE_CHECKOUT => $data[static::TYPE_CHECKOUT],
        ];
    }
}
