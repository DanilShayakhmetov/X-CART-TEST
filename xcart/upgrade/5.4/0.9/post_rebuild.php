<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    // Loading data to the database from yaml file
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';
    \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);

    changeNotificationTranslations();

    \XLite\Core\Database::getEM()->flush();

    updateSystemDataStorage();
};

function changeNotificationTranslations()
{
    $result = false;

    $notificationsToChange = [
        'order_processed' => [
            'old' => '<h2 dir="ltr">Your order was paid successfully, %recipient_name%!</h2><h3 dir="ltr">And we are just as excited as you are</h3><p dir="ltr"><br>Take a look below for all the confirmation details you&rsquo;ll need. We will be back in a moment to let you know that your item is on its way, unless you decide to pick it up yourself.&nbsp;</p>',
            'new' => '<h2 dir="ltr">Your order was paid successfully, %recipient_name%!</h2><h3 dir="ltr">And we are just as excited as you are</h3><p dir="ltr"><br>Take a look below for all the confirmation details you&rsquo;ll need.</p>'
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

function updateSystemDataStorage()
{
    $systemData = \XLite\Core\Marketplace::getInstance()->getSystemData();

    $systemData['adminEmail']      = getAdminEmail();
    $systemData['shopCountryCode'] = \XLite\Core\Config::getInstance()->Company->location_country;

    \XLite\Core\Marketplace::getInstance()->setSystemData($systemData);
}

function getAdminEmail()
{
    $email = \XLite\Core\Mailer::getSiteAdministratorMail();

    if (!$email) {
        // Search for first active root administrator
        $cnd = new \XLite\Core\CommonCell;
        $cnd->{\XLite\Model\Repo\Profile::SEARCH_PERMISSIONS} = \XLite\Model\Role\Permission::ROOT_ACCESS;
        $cnd->{\XLite\Model\Repo\Profile::P_ORDER_BY} = array('p.profile_id');
        $rootAdmins = \XLite\Core\Database::getRepo('XLite\Model\Profile')->search($cnd);

        if ($rootAdmins) {
            foreach ($rootAdmins as $admin) {
                if ($admin->isAdmin() && $admin->isEnabled()) {
                    $email = $admin->getLogin();
                    break;
                }
            }
        }
    }

    return $email;
}
