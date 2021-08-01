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
};

function changeNotificationTranslations()
{
    $result = false;

    $notificationsToChange = [
        'low_limit_warning' => [
            'old' => '<h3 dir="ltr">Heads-up!</h3><p dir="ltr">You&rsquo;re about to stock out! According to our stats, you&rsquo;ll likely sell the last item of this product by %latest_sale_date%. Since you can&rsquo;t sell what you don&rsquo;t have, consider replenishing the product ASAP.</p><p dir="ltr">%dynamic_message%</p>',
            'new' => '<h3 dir="ltr">You&rsquo;re running out soon!</h3><p dir="ltr"> Looks like you sold a few items of this product %latest_sale_date% and you only have %product_qty% items left. Since you can&rsquo;t sell what you don&rsquo;t have, consider restocking the item as soon as possible.</p><p dir="ltr">%dynamic_message%</p>'
        ],
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
