<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Mail;


use XLite\Core\Mail\AMail;
use XLite\Core\Mail\Registry;
use XLite\View\Mailer;

 class Sender extends \XLite\Core\Mail\SenderAbstract implements \XLite\Base\IDecorator
{
    public static function getNotificationEditableContent($dir, $data, $interface)
    {
        $mail = Registry::createNotification($interface, $dir, $data);
        $mailer = static::getMailerForMail(
            $mail,
            [
                static::getDataProcessor($mail),
                static::getThemeTweakerMailerProcessor($mail),
            ]
        );

        $variablesProcessor = static::getVariablesPopulateProcessor($mail);
        return $variablesProcessor($mailer->getNotificationEditableContent($interface));
    }

    public static function getNotificationPreviewContent($dir, $data, $interface)
    {
        $mail = Registry::createNotification($interface, $dir, $data);
        $mailer = static::getMailerForMail(
            $mail,
            [
                static::getDataProcessor($mail),
                static::getThemeTweakerMailerProcessor($mail),
            ]
        );

        $variablesProcessor = static::getVariablesPopulateProcessor($mail);
        return $variablesProcessor($mailer->getNotificationPreviewContent($interface));
    }

    protected static function getThemeTweakerMailerProcessor(AMail $mail)
    {
        return function (Mailer $mailer) use ($mail) {
            $mailer->set('dir', $mail::getDir());

            return $mailer;
        };
    }
}