<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
function updatePaymentMethods() {
    $methods = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
        ->findBy([
            'added' => 1,
            'fromMarketplace' => 1
        ]);

    if (empty($methods)) {
        return;
    }

    /** @var \XLite\Model\Payment\Method $method */
    foreach ($methods as $method) {
        $method->setFromMarketplace(false);
    }
}


return function () {
    updatePaymentMethods();

    \XLite\Core\Database::getEM()->flush();
};