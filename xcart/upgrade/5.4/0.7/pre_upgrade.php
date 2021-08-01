<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
function isCollission($link1, $link2)
{
    return $link1->getPaymentMethod() == $link2->getPaymentMethod()
        && $link1->getName() == $link2->getName()
        && $link1->getSettingId() < $link2->getSettingId();
}

function removePaymentSettingCollision()
{
    $links = \XLite\Core\Database::getRepo('XLite\Model\Payment\MethodSetting')->findAll();
    foreach ($links as $k => $link) {
        for ($i = 0; $i < $k; $i++) {
            if (isCollission($links[$i], $link)) {
                \XLite\Core\Database::getEM()->remove($link);
            }
        }
    }
}

return function () {
    removePaymentSettingCollision();

    \XLite\Core\Database::getEM()->flush();
};