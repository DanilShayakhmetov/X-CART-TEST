<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    if (changeNotificationTranslations()) {
        \XLite\Core\Database::getEM()->flush();
    }
};

function changeNotificationTranslations()
{
    $result = false;

    $notificationsToChange = [
        'modules/XC/ProductVariants/low_variant_limit_warning' => [
            'old' => '<h3 dir="ltr">Heads up, boss!</h3><p dir="ltr">I see that some of your product variants are about to run out of stock.</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">It&rsquo;s high time to replenish your supplies. You are not going to sell fresh air, are you?</p>',
            'new' => '<h3 dir="ltr">Heads up!</h3><p dir="ltr">I see that some of your product variants are about to run out of stock.</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">It&rsquo;s high time to replenish your supplies. You are not going to sell fresh air, are you?</p>'
        ]
    ];

    foreach ($notificationsToChange as $id => $adminText) {
        $notification = \XLite\Core\Database::getRepo('XLite\Model\Notification')->find($id);

        if ($notification && $notification->getAdminText() === $adminText['old']) {
            $notification->setAdminText($adminText['new']);
            $result = true;
        }
    }

    return $result;
}
