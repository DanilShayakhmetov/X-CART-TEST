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
        'modules/CDev/ContactUs/message' => [
            'old' => '<h3 dir="ltr">Hey, </h3><p dir="ltr">%message_author% has sent you a message through the &ldquo;Contact us&rdquo; form and is waiting for your answer. Come on, reply to this email and drop him a line or two.</p><blockquote><p dir="ltr">%message%</p></blockquote>',
            'new' => '<p dir="ltr">%message_author% has sent you a message through the “Contact us” form and is waiting for your reply. Bust out the killer customer care and get in touch with him or her in a timely manner.</p><blockquote><p dir="ltr">%message%</p></blockquote>',
            'greetingEnabled' => true,
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