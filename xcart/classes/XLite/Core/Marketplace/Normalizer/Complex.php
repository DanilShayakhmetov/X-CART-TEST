<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Marketplace\Normalizer;


class Complex extends \XLite\Core\Marketplace\Normalizer
{
    protected $normalizers = [
        'banners'             => 'XLite\Core\Marketplace\Normalizer\Banners',
        'notifications'       => 'XLite\Core\Marketplace\Normalizer\Notifications',
        'payment_methods'     => 'XLite\Core\Marketplace\Normalizer\PaymentMethods',
        'shipping_methods'    => 'XLite\Core\Marketplace\Normalizer\ShippingMethods',
        'marketplace_modules' => 'XLite\Core\Marketplace\Normalizer\MarketplaceModules',
    ];

    /**
     * @param array $response
     *
     * @return array
     */
    public function normalize($response)
    {
        if (is_array($response)) {
            foreach ($response as $type => $data) {
                if (isset($this->normalizers[$type])) {
                    $response[$type] = (new $this->normalizers[$type])->normalize($response);
                }
            }

            return $response;
        }

        return [];
    }
}