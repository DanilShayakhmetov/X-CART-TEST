<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function () {
    if (changeNotificationTranslations()) {
        \XLite\Core\Database::getEM()->flush();
    }
};

function changeNotificationTranslations()
{
    $result = false;

    $notificationsToChange = [
        'modules/XC/Reviews/review_key' => [
            'old' => '<p>Thank you for purchasing our products!</p><p>%dynamic_message%</p><p>We hope you love them. And if you really do, please take a minute to review your order to let others know that we care about delivering the best quality.</p>',
            'new' => '<p>Thank you for purchasing our products!</p><p>We hope you love them. And if you really do, please take a minute to review your order to let others know that we care about delivering the best quality.</p><p>%dynamic_message%</p>'
        ],
    ];

    foreach ($notificationsToChange as $id => $adminText) {
        $notification = \XLite\Core\Database::getRepo('XLite\Model\Notification')->find($id);

        if ($notification && $notification->getCustomerText() === $adminText['old']) {
            $notification->setCustomerText($adminText['new']);
            $result = true;
        }
    }

    return $result;
}