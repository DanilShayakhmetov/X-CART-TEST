<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    if (changeAdminNotificationTranslations()) {
        \XLite\Core\Database::getEM()->flush();
    }
};

function changeAdminNotificationTranslations()
{
    $notificationsToChange = [
        'modules/XC/ProductVariants/low_variant_limit_warning' => [
            'old' => '<h3 dir="ltr">Heads up!</h3><p dir="ltr">I see that some of your product variants are about to run out of stock.</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">It&rsquo;s high time to replenish your supplies. You are not going to sell fresh air, are you?</p>',
            'new' => '<h3 dir="ltr">Heads up!</h3><p dir="ltr">Variety is the spice of life and one of your spices is dangerously low. That&rsquo;s right, some variants of a product are about to run out of stock. Consider replenishing ASAP so you keep selling and bringing the flavor (and color and style) that your customers want.</p>',
        ],
    ];

    foreach ($notificationsToChange as $id => $data) {
        /** @var \XLite\Model\Notification $notification */
        $notification = \XLite\Core\Database::getRepo('XLite\Model\Notification')->find($id);

        if ($notification && $notification->getTranslation('en')) {
            if (isset($data['description'])) {
                $notification->getTranslation('en')->setDescription($data['description']);
            }

            if ($notification->getTranslation('en')->getAdminText() === $data['old']) {
                $notification->getTranslation('en')->setAdminText($data['new']);

                if (isset($data['greetingEnabled'])) {
                    $notification->setAdminGreetingEnabled($data['greetingEnabled']);
                }

                if (isset($data['subject'])) {
                    $notification->getTranslation('en')->setAdminSubject($data['subject']);
                }
            }
        }
    }

    return true;
}