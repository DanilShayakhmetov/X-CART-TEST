<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function () {
    if ($notification = \XLite\Core\Database::getRepo('XLite\Model\Notification')->find('modules/CDev/PINCodes/acquire_pin_codes_failed')) {
        $notification->delete();
    }
};
