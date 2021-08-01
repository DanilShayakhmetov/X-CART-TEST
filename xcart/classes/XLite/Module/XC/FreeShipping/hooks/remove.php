<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return new \XLite\Rebuild\Hook(
    function () {
        $shippingMethods = \XLite\Core\Database::getRepo(\XLite\Model\Shipping\Method::class)
            ->findBy(['code' => ['FREESHIP', 'FIXEDFEE']]);
        foreach ($shippingMethods as $method) {
            \XLite\Core\Database::getEM()->remove($method);
        }

        \XLite\Core\Database::getEM()->flush();
    }
);
