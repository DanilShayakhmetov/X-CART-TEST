<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    $conciergeConfig = \XLite\Core\Config::getInstance()->XC->Concierge;
    if (stripos($conciergeConfig->user_id, '@x-cart.com') !== false) {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOptions(
            [
                [
                    'category' => 'XC\Concierge',
                    'name'     => 'write_key',
                    'value'    => '',
                ],
                [
                    'category' => 'XC\Concierge',
                    'name'     => 'user_id',
                    'value'    => '',
                ],
                [
                    'category' => 'XC\Concierge',
                    'name'     => 'additional_config_loaded',
                    'value'    => 'false',
                ],
            ]
        );
    }
};
