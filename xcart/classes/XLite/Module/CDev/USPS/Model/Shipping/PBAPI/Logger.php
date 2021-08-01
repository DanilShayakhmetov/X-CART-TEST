<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model\Shipping\PBAPI;

use XLite\Module\CDev\USPS\Main;

class Logger implements ILogger
{
    /**
     * @param mixed $message
     */
    public function log($message)
    {
        Main::log($message);
    }
}
