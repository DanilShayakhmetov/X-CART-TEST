<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    $conciergeConfig = \XLite\Core\Config::getInstance()->XC->Concierge;
    if ($conciergeConfig->additional_config_loaded !== 'true'
        && $conciergeConfig->user_id == ''
        && $conciergeConfig->write_key == ''
    ) {
        \XLite\Module\XC\Concierge\Main::fillDefaultConciergeOptions();
    }
};
