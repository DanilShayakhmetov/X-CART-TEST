<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Notifications;


use XLite\Core\Auth;
use XLite\Core\Converter;
use XLite\Core\Request;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data\Constant;

abstract class StaticProviderAbstract
{
    public static function getProvidersForNotification($dir)
    {
        $result = [];

        $data = static::getNotificationsStaticData();

        if (!empty($data[$dir])) {
            foreach ($data[$dir] as $name => $datum) {
                $result[] = new Constant(
                    $name,
                    $datum,
                    $dir
                );
            }
        }

        return $result;
    }

    protected static function getNotificationsStaticData()
    {
        return [
            'failed_admin_login' => [
                'login' => 'admin@example.com',
                'REMOTE_ADDR' => '127.0.0.1',
                'HTTP_X_FORWARDED_FOR' => 'localhost',
                'HTTP_REFERER' => Converter::buildFullURL(),
            ],
            'failed_transaction' => [
                'transactionSearchURL' => Converter::buildFullURL('payment_transactions', '', [
                    'public_id' => 'failed_transaction_id_placeholder'
                ]),
            ],
            'profile_deleted' => [
                'deletedLogin' => 'deleted@example.com',
            ],
            'recover_password_request' => [
                'profile' => Auth::getInstance()->getProfile(),
                'resetKey' => 'profile_reset_key_placeholder'
            ],
            'register_anonymous' => [
                'password' => 'new_password_placeholder',
            ],
        ];
    }
}