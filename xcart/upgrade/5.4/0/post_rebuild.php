<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    removeEmailNotificationTranslations540();

    // Loading data to the database from yaml file
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';

    if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
    }

    removeConfigOptions540();
    removeEmailNotifications540();

    \XLite\Core\Database::getEM()->flush();
};

function removeConfigOptions540()
{
    $options = \XLite\Core\Database::getRepo('XLite\Model\Config')->findBy([
        'name' => [
            'admin_presentation',
            'products_per_page_admin',
            'users_per_page',
            'orders_per_page',
        ],
        'category' => 'General'
    ]);

    foreach ($options as $option) {
        \XLite\Core\Database::getEM()->remove($option);
    }
}

function removeEmailNotificationTranslations540()
{
    $qb = \XLite\Core\Database::getRepo('XLite\Model\NotificationTranslation')->createPureQueryBuilder('nt');
    $qb->delete()
        ->where($qb->expr()->neq('nt.code', ':default_lang'))
        ->setParameter('default_lang', 'en')
        ->getQuery()->execute();
}

function removeEmailNotifications540()
{
    $qb = \XLite\Core\Database::getRepo('XLite\Model\Notification')->createPureQueryBuilder();
    $alias = $qb->getMainAlias();
    $qb->delete()
        ->where($qb->expr()->in("$alias.templatesDirectory", [
            'profile_modified',
            'order_advanced_changed',
        ]));
    $qb->getQuery()
        ->execute();
}
