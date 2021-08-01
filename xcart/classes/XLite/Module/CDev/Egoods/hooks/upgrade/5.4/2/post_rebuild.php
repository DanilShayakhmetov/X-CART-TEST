<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function () {
    // Loading data to the database from yaml file
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';

    if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
    }

    changeCustomerNotificationTranslations();

    \XLite\Core\Database::getEM()->flush();
};

function changeCustomerNotificationTranslations()
{
    $notificationsToChange = [
        'modules/CDev/Egoods/egoods_links' => [
            'old' => '<h2 dir="ltr">Your order is ready, %recipient_name%!</h2><h3 dir="ltr">And we are just as excited as you are<br><br></h3><p dir="ltr">Take a look below for the files you&rsquo;ve just purchased:</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">And if something is not working, feel free to contact us.</p>',
            'new' => '<h2 dir="ltr" style="text-align: center;">Your order is ready, %recipient_name%!</h2><h3 dir="ltr" style="text-align: center;">And we are just as excited as you are<br><br></h3><p dir="ltr">The items listed below have been paid for and are ready for download!</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">If you encounter any difficulty downloading, feel free to contact us.</p>',
            'subject' => 'Download your digital purchase',
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