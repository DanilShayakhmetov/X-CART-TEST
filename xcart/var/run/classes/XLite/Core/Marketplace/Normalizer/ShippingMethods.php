<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Marketplace\Normalizer;

class ShippingMethods extends \XLite\Core\Marketplace\Normalizer
{
    /**
     * @param array $response
     *
     * @return array
     */
    public function normalize($response)
    {
        return $response['shipping_methods'] ?? [];
    }
}
