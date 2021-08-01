<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\PaypalCommercePlatform;

use PayPalHttp\HttpRequest;

class GenerateClientToken extends HttpRequest
{
    function __construct()
    {
        parent::__construct("/v1/identity/generate-token", "POST");
        $this->headers["Content-Type"] = "application/json";
    }
}
