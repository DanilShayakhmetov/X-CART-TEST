<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Core;


/**
 * @inheritdoc
 */
class Promo extends \XLite\Core\Promo implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    protected function getPromoList()
    {
        return parent::getPromoList() + [
            'geolocation-1' => [
                'module'    => 'XC\Geolocation',
                'content'   => 'Geolocation promo block'
            ],
        ];
    }
}