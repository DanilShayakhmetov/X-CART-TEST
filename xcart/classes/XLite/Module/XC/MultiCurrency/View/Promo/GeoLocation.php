<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\Promo;


/**
 * GeoLocation promo block
 *
 * @ListChild (list="active_currencies.before", zone="admin")
 */
class GeoLocation extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/MultiCurrency/promo/geolocation.twig';
    }
}