<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Marketplace\Normalizer;

use XLite\Core\Marketplace\Constant;

class Banners extends \XLite\Core\Marketplace\Normalizer
{
    protected $map = [
        'image'   => Constant::FIELD_BANNER_IMG,
        'module'  => Constant::FIELD_BANNER_MODULE,
        'url'     => Constant::FIELD_BANNER_URL,
        'section' => Constant::FIELD_BANNER_SECTION,
    ];

    /**
     * @param array $response
     *
     * @return array
     */
    public function normalize($response)
    {
        $result = isset($response['banners'])
            ? array_map(function ($e) {
                return $this->mapFields($e, $this->map);
            }, array_filter($response['banners'], 'is_array'))
            : [];

        return $result;
    }
}