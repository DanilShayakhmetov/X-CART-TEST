<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\ItemsList\Model;

/**
 * @Decorator\Depend ("XC\Add2CartPopup")
 */
class PaypalButtonAdd2CartPopup extends \XLite\Module\CDev\Paypal\View\ItemsList\Model\PaypalButton implements \XLite\Base\IDecorator
{
    /**
     * Types
     */
    const TYPE_ADD2CART_POPUP = 'add2cart';

    /**
     * Get plain data
     *
     * @return array
     */
    protected function getPlainData()
    {
        $data = parent::getPlainData();

        $data[static::TYPE_ADD2CART_POPUP] = [
            'location'     => static::t('pp-button-location:Add2Cart popup'),
            'size'         => $this->getStyleValue(static::TYPE_ADD2CART_POPUP, 'size'),
            'color'        => $this->getStyleValue(static::TYPE_ADD2CART_POPUP, 'color'),
            'shape'        => $this->getStyleValue(static::TYPE_ADD2CART_POPUP, 'shape'),
        ];

        return $data;
    }
}
