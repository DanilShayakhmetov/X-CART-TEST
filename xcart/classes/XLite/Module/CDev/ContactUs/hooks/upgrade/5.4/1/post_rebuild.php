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
    
    changeNotificationTranslations();

    \XLite\Core\Database::getEM()->flush();
};

function changeNotificationTranslations()
{
    $result = false;

    $notificationsToChange = [
        'modules/CDev/ContactUs/message' => [
            'old' => '<h3 dir="ltr">Hey boss, </h3><p dir="ltr">%message_author% has sent you a message through the &ldquo;Contact us&rdquo; form and is waiting for your answer. Come on, reply to this email and drop him a line or two.</p><blockquote><p dir="ltr">%message%</p></blockquote>',
            'new' => '<h3 dir="ltr">Hey, </h3><p dir="ltr">%message_author% has sent you a message through the &ldquo;Contact us&rdquo; form and is waiting for your answer. Come on, reply to this email and drop him a line or two.</p><blockquote><p dir="ltr">%message%</p></blockquote>'
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
