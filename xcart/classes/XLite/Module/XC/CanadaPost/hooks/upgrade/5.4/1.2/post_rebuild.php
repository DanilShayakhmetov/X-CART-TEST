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
        'modules/XC/CanadaPost/return_approved' => [
            'old' => '<p dir="ltr">It&rsquo;s a pity to know that our products didn&rsquo;t sit well with you for some reason.&nbsp;</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">Anyway, we are ready to take the items back for a full refund. Please, <a href="https://www.canadapost.ca/web/en/kb/details.page?article=how_do_i_return_a_re&cattype=kb&cat=atthepostoffice&subcat=services">ship the package back to our office</a> using the parcel service that best meets your needs.&nbsp;</p><p dir="ltr">Feel free to contact us if you have any questions.</p>',
            'new' => '<p dir="ltr">We&rsquo;re sorry to hear that your order didn&rsquo;t work out as hoped.</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">We&rsquo;re happy to take the items back for a full refund and we hope we can better meet your needs in the future. Please, <a href="https://www.canadapost.ca/web/en/kb/details.page?article=how_do_i_return_a_re&cattype=kb&cat=atthepostoffice&subcat=services">ship the package back to our office</a> using the parcel service that best meets your needs.&nbsp;</p><p dir="ltr">Feel free to contact us if you have any questions.</p>',
        ],
        'modules/XC/CanadaPost/return_rejected' => [
            'old' => '<h3 dir="ltr">Hello %recipient_name%,</h3><p dir="ltr">We are awfully sorry, but we cannot take your products back and make a refund according to our money back policy.</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">Please contact us if you have any questions.</p>',
            'new' => '<h3 dir="ltr">Hello %recipient_name%,</h3><p dir="ltr">Unfortunately, we cannot take your products or issue a refund according to our stated policy.</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">Please contact us if you have any questions.</p>',
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