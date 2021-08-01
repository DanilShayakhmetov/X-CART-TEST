<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Request;

class MerchantInfo extends Request
{
    public function __construct($endpoint, $token, $developerId, $emailId)
    {
        parent::__construct(
            $endpoint . '/shippingservices/v1/developers/' . $developerId . '/merchants/emails/' . $emailId . '/',
            'GET',
            null,
            [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ]
        );
    }
}
