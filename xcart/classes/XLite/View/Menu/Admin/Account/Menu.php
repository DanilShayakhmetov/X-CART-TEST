<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\Account;

/**
 * Quick menu widget
 */
class Menu extends \XLite\View\Menu\Admin\AAdmin
{
    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'menu/account';
    }

    /**
     * Get default widget
     *
     * @return string
     */
    protected function getDefaultWidget()
    {
        return 'XLite\View\Menu\Admin\Account\Node';
    }

    /**
     * Define menu items
     *
     * @return array
     */
    protected function defineItems()
    {
        $result = [
            'profile'      => [
                static::ITEM_TITLE         => static::t('My profile'),
                static::ITEM_TARGET        => 'profile',
                static::ITEM_WEIGHT        => 100,
                static::ITEM_PUBLIC_ACCESS => true,
            ],
            'account_info' => [
                static::ITEM_TITLE         => \XLite\Core\Auth::getInstance()->getProfile()->getLogin(),
                static::ITEM_CLASS         => 'login-text',
                static::ITEM_WEIGHT        => 200,
                static::ITEM_PUBLIC_ACCESS => true,
            ],

            'knoweledge_base' => [
                static::ITEM_TITLE      => static::t('Knowledge Base'),
                static::ITEM_LINK       => static::t('https://kb.x-cart.com/'),
                static::ITEM_CLASS      => 'knoweledge-base external',
                static::ITEM_WEIGHT     => 300,
                static::ITEM_BLANK_PAGE => true,
            ],
            'developers_docs' => [
                static::ITEM_TITLE      => static::t('Developers docs'),
                static::ITEM_LINK       => 'https://devs.x-cart.com/',
                static::ITEM_CLASS      => 'developers-docs external',
                static::ITEM_WEIGHT     => 400,
                static::ITEM_BLANK_PAGE => true,
            ],
            'report_bug'      => [
                static::ITEM_TITLE      => static::t('Report a bug'),
                static::ITEM_LINK       => 'mailto:support@x-cart.com?subject=Report%20a%20Bug&body=WHAT%20STEPS%20WILL%20REPRODUCE%20THE%20PROBLEM%3F%0D%0A1.%0D%0A2.%0D%0A..%0D%0AWHAT%20IS%20THE%20EXPECTED%20RESULT%3F%0D%0A1.%0D%0A2.%0D%0A..%0D%0AWHAT%20HAPPENS%20INSTEAD%3F%0D%0A1.%0D%0A2.%0D%0A..%0D%0A',
                static::ITEM_CLASS      => 'report-bug external',
                static::ITEM_WEIGHT     => 600,
                static::ITEM_BLANK_PAGE => true,
            ],
            'logoff' => [
                static::ITEM_TITLE         => static::t('Sign out'),
                static::ITEM_CLASS         => 'logoff',
                static::ITEM_TARGET        => 'login',
                static::ITEM_EXTRA         => ['action' => 'logoff'],
                static::ITEM_WEIGHT        => 100000,
                static::ITEM_PUBLIC_ACCESS => true,
            ],
        ];

        $installationData = \XLite\Core\Marketplace::getInstance()->getInstallationData();

        $purchasesCount = $installationData['purchasesCount'] ?? 0;
        if ($purchasesCount) {
            $result['my_purchases'] = [
                static::ITEM_TITLE      => static::t('My purchases'),
                static::ITEM_LABEL      => $purchasesCount,
                static::ITEM_LABEL_LINK => \XLite::getInstance()->getServiceURL('#/my-purchases'),
                static::ITEM_CLASS      => 'my-purchases',
                static::ITEM_WEIGHT     => 250,
            ];
        }

        return $result;
    }
}
