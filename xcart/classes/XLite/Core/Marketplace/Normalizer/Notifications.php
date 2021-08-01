<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Marketplace\Normalizer;

use XLite\Core\Marketplace\Constant;

class Notifications extends \XLite\Core\Marketplace\Normalizer
{
    protected $map = [
        'type'        => Constant::FIELD_NOTIFICATION_TYPE,
        'module'      => Constant::FIELD_NOTIFICATION_MODULE,
        'image'       => Constant::FIELD_NOTIFICATION_IMAGE,
        'title'       => Constant::FIELD_NOTIFICATION_TITLE,
        'description' => Constant::FIELD_NOTIFICATION_DESCRIPTION,
        'link'        => Constant::FIELD_NOTIFICATION_LINK,
        'date'        => Constant::FIELD_NOTIFICATION_DATE,
        'pageParams'  => Constant::FIELD_NOTIFICATION_PAGE_PARAMS,
    ];

    /**
     * @param array $response
     *
     * @return array
     */
    public function normalize($response)
    {
        $result = isset($response['notifications'])
            ? array_map(function ($e) {
                return $this->mapFields($e, $this->map);
            }, array_filter($response['notifications'], 'is_array'))
            : [];

        return $result;
    }

}