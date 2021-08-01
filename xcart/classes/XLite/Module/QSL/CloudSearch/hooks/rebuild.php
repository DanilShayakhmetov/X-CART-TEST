<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

use XLite\Module\QSL\CloudSearch\Core\RegistrationScheduler;
use XLite\Rebuild\Hook;

return new Hook(
    function () {
        RegistrationScheduler::getInstance()->schedule();
    }
);