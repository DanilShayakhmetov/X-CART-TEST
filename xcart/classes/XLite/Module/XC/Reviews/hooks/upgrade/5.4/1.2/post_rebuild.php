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

    if (changeCustomerNotificationTranslations()) {
        \XLite\Core\Database::getEM()->flush();
    }
};

function changeAdminNotificationTranslations()
{
    $notificationsToChange = [
        'modules/XC/Reviews/new_review' => [
            'old' => '<p dir="ltr">%author_name% has just rated your &quot;%product_name%&quot; and left a review for you.</p><p dir="ltr">%dynamic_message%</p><blockquote><p dir="ltr">%review%</p></blockquote><p dir="ltr">Follow <a href="%product_link%">this link</a> to approve or remove this review. And don&rsquo;t forget to reply to your customer &mdash; we all love to be heard ;)</p>',
            'new' => '<p dir="ltr">%author_name% has just rated your &quot;%product_name%&quot; and left a review for you.</p><p dir="ltr">%dynamic_message%</p><blockquote><p dir="ltr">%review%</p></blockquote><p dir="ltr">Follow <a href="%product_link%">this link</a> to approve or remove this review. And don&rsquo;t forget to reply to the customer. If there&rsquo;s a problem, now&rsquo;s your chance to make it right and be a customer-service hero. If you&rsquo;ve gotten a rave review, reach out, thank the reviewer, and ask if there&rsquo;s anything else you can help with. Both scenarios are great opportunities to build strong relationships and an excellent reputation.</p>',
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

function changeCustomerNotificationTranslations()
{
    $notificationsToChange = [
        'modules/XC/Reviews/review_key' => [
            'old' => '<p>Thank you for purchasing our products!</p><p>We hope you love them. And if you really do, please take a minute to review your order to let others know that we care about delivering the best quality.</p><p>%dynamic_message%</p>',
            'new' => '<p>Thank you for purchasing our products! We hope you love them.</p><p>%dynamic_message%</p><p>Please take a minute to review your order to let others know that you&rsquo;ve found a gem and want to share the goodness!</p>',
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