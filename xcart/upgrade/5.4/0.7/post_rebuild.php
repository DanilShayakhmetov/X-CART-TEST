<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{

    $options = \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->findBy([
        'name' => [
            'https://kb.x-cart.com/taxes/setting_up_vat_gst.html',
            'https://kb.x-cart.com/taxes/setting_up_canadian_taxes.html',
            'https://kb.x-cart.com/taxes/setting_up_sales_tax.html',
            'Setting up VAT / GST',
            'Setting up sales tax'
        ]
    ]);
    $options_translations = \XLite\Core\Database::getRepo('XLite\Model\LanguageLabelTranslation')->findBy([
        'label' => [
            'https://kb.x-cart.com/taxes/setting_up_vat_gst.html',
            'https://kb.x-cart.com/taxes/setting_up_canadian_taxes.html',
            'https://kb.x-cart.com/taxes/setting_up_sales_tax.html',
            'Setting up VAT / GST',
            'Setting up sales tax'
        ]
    ]);
    foreach ($options as $option) {
        \XLite\Core\Database::getEM()->remove($option);
    }
    foreach ($options_translations as $option_translation) {
        \XLite\Core\Database::getEM()->remove($option_translation);
    }

    \XLite\Core\Database::getEM()->flush();
    
    // Loading data to the database from yaml file
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';
    \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);

    removeConfigOptions();
    changeNotificationTranslations();

    \XLite\Core\Database::getEM()->flush();
};

function removeConfigOptions()
{
    $options = \XLite\Core\Database::getRepo('XLite\Model\Config')->findBy([
        'name'     => 'quick_data_rebuilding',
        'category' => 'CacheManagement'
    ]);

    foreach ($options as $option) {
        \XLite\Core\Database::getEM()->remove($option);
    }
}

function changeNotificationTranslations()
{
    $result = false;

    $notificationsToChange = [
        'low_limit_warning' => [
            'old' => '<h3 dir="ltr">Heads-up boss, </h3><p dir="ltr">I see that some of your products are about to run out of stock.&nbsp;</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">According to our stats you&rsquo;ll sell the last item by %latest_sale_date%. So it&rsquo;s high time to replenish them. You are not going to sell fresh air, are you?</p>',
            'new' => '<h3 dir="ltr">Heads-up, </h3><p dir="ltr">I see that some of your products are about to run out of stock.&nbsp;</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">According to our stats you&rsquo;ll sell the last item by %latest_sale_date%. So it&rsquo;s high time to replenish them. You are not going to sell fresh air, are you?</p>'
        ],
        'order_created' => [
            'old' => '<p dir="ltr">Your wallet is going to get heavier very soon. Go ahead, show your customer you care and process the order ASAP.</p>',
            'new' => '<p dir="ltr">There’s a new order for you. Go ahead, show your customer you care and process the order ASAP.</p>'
        ],
        'order_processed' => [
            'old' => '<p dir="ltr">Your wallet has got a little heavier. Way to go!</p>',
            'new' => '<p dir="ltr">Your order has been processed. Way to go!</p>'
        ]
    ];

    foreach ($notificationsToChange as $id => $adminText) {
        $notification = \XLite\Core\Database::getRepo('XLite\Model\Notification')->find($id);

        if ($notification && $notification->getAdminText() === $adminText['old']) {
            $notification->setAdminText($adminText['new']);
            $result = true;
        }
    }

    $label = \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->findOneByName('emailNotificationAdminGreeting');
    if (
        $label &&
        $label->getTranslation('en') &&
        $label->getTranslation('en')->getLabel() === '<h3>Hey boss</h3>'
    ) {
        $label->getTranslation('en')->setLabel('<h3>Hey</h3>');
        $result = true;
    }

    return $result;
}
