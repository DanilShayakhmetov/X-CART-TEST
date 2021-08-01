<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function () {
    \XLite\Core\TmpVars::getInstance()->add_product_tileClosed = 1;
    \XLite\Core\TmpVars::getInstance()->company_logo_tileClosed = 1;
    \XLite\Core\TmpVars::getInstance()->demo_catalog_tileClosed = 1;

    if ('disabled' === \XLite\Core\Config::getInstance()->XC->Onboarding->wizard_state){
        \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption([
            'category' => 'XC\Onboarding',
            'name'     => 'wizard_force_disabled',
            'value'    => true,
        ]);
    }
};
