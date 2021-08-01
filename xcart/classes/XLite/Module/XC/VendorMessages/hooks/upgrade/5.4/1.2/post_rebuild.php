<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    if (changeCustomerNotificationTranslations()) {
        \XLite\Core\Database::getEM()->flush();
    }
};

function changeCustomerNotificationTranslations()
{
    $notificationsToChange = [
        'modules/XC/VendorMessages/notification' => [
            'old' => '<p dir="ltr">I see there&rsquo;s a new message in your account. It&rsquo;s about the order <a href="%order_link%">#%order_number%</a>, and it is waiting for your reply.&nbsp;</p><blockquote><p dir="ltr">%message%</p></blockquote>',
            'new' => '<p dir="ltr">You have a new message about order <a href="%order_link%">#%order_number%</a>. Please review the message and take any necessary action.</p><blockquote><p dir="ltr">%message%</p></blockquote>',
            'description' => 'This message will be sent to the customer when a new message appears in the communication thread regarding an order',
            'subject' => 'Order #%order_number%: new message from seller',
        ],
    ];

    foreach ($notificationsToChange as $id => $data) {
        /** @var \XLite\Model\Notification $notification */
        $notification = \XLite\Core\Database::getRepo('XLite\Model\Notification')->find($id);

        if ($notification && $notification->getTranslation('en')) {
            if (isset($data['description'])) {
                $notification->getTranslation('en')->setDescription($data['description']);
            }

            if ($notification->getTranslation('en')->getCustomerText() === $data['old']) {
                $notification->getTranslation('en')->setCustomerText($data['new']);

                if (isset($data['greetingEnabled'])) {
                    $notification->setCustomerGreetingEnabled($data['greetingEnabled']);
                }

                if (isset($data['subject'])) {
                    $notification->getTranslation('en')->setCustomerSubject($data['subject']);
                }
            }
        }
    }

    return true;
}