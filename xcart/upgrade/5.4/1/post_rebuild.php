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
    setLogoAltTranslations();
    fillDefaultDisplayMode();
    removeDuplicatedLayouts();

    \XLite\Core\Database::getEM()->flush();
};

function changeNotificationTranslations()
{
    $result = false;

    $notificationsToChange = [
        'order_processed' => [
            'old' => '<h2 dir="ltr">Your order was paid successfully, %recipient_name%!</h2><h3 dir="ltr">And we are just as excited as you are</h3><p dir="ltr"><br>Take a look below for all the confirmation details you&rsquo;ll need.</p>',
            'new' => '<h2 dir="ltr">%recipient_name%, your order has been paid successfully.</h2><h3 dir="ltr">And we are just as excited as you are</h3><p dir="ltr"><br>Take a look below for all the confirmation details you&rsquo;ll need.</p>'
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

function setLogoAltTranslations()
{
    $homeLabel = \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->findOneBy(['name' => 'Home']);

    if ($homeLabel) {
        $logoAltLabel = \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->findOneBy(['name' => 'Logo alt']);
        if ($logoAltLabel) {
            \XLite\Core\Database::getEM()->remove($logoAltLabel);
            \XLite\Core\Database::getEM()->flush();
        }

        $logoAltLabel = $homeLabel->cloneEntity();
        $logoAltLabel->setName('Logo alt');
        \XLite\Core\Database::getEM()->persist($logoAltLabel);
    }
}

function fillDefaultDisplayMode()
{
    \XLite\Core\Database::getRepo(\XLite\Model\Attribute::class)
        ->createPureQueryBuilder()
        ->update(\XLite\Model\Attribute::class, 'a')
        ->set('a.displayMode', ':selectBoxMode')
        ->setParameter('selectBoxMode', \XLite\Model\Attribute::SELECT_BOX_MODE)
        ->getQuery()
        ->execute();

    \XLite\Core\Database::getRepo(\XLite\Model\AttributeProperty::class)
        ->createPureQueryBuilder()
        ->update(\XLite\Model\AttributeProperty::class, 'ap')
        ->set('ap.displayMode', ':selectBoxMode')
        ->setParameter('selectBoxMode', \XLite\Model\Attribute::SELECT_BOX_MODE)
        ->getQuery()
        ->execute();
}

function removeDuplicatedLayouts()
{
    $array = [];
    $q     = \XLite\Core\Database::getEM()->createQuery(
        'SELECT vl FROM XLite\Model\ViewList vl WHERE vl.parent IS NOT NULL'
    );

    $iterableResult = $q->iterate();
    foreach ($iterableResult as $c) {
        $array[$c[0]->parent->list_id . ' ' . $c[0]->child . ' ' . $c[0]->list][] = $c;
    }

    foreach ($array as $k => $subarray) {
        if (count($subarray) > 1) {
            usort($subarray, static function ($a, $b) {
                if ($a[0]->override_mode < $b[0]->override_mode) {
                    return -1;

                } elseif ($a[0]->override_mode > $b[0]->override_mode) {
                    return 1;
                }

                return 0;
            });

            foreach ($subarray as $k => $element) {
                if ($k) {
                    \XLite\Core\Database::getEM()->remove($element[0]);
                }
            }
        }
    }
}