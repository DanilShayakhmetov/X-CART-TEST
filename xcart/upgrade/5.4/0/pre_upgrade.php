<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function backupNotificationTexts540()
{
    $qb = \XLite\Core\Database::getRepo('XLite\Model\NotificationTranslation')->createPureQueryBuilder();
    $alias = $qb->getMainAlias();
    $qb->select(
        "owner.templatesDirectory," .
        "$alias.code," .
        "$alias.customerSubject," .
        "$alias.customerText," .
        "$alias.adminSubject," .
        "$alias.adminText"
    );
    $qb->innerJoin("$alias.owner", 'owner');

    $handle = fopen(LC_DIR_VAR . 'notifications_backup_pre_540.csv', 'w');

    fputcsv($handle, [
        'templatesDirectory',
        'code',
        'customerSubject',
        'customerText',
        'adminSubject',
        'adminText',
    ]);

    foreach ($qb->getQuery()->getResult() as $notificationData) {
        fputcsv($handle, $notificationData);
    }

    fclose($handle);
}

return function () {
    backupNotificationTexts540();
};