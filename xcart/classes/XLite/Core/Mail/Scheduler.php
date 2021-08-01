<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail;


use XLite\Core\Job\SchedulingJobs;
use XLite\Core\Job\SendMail;
use XLite\Core\Mail\Common\SafeMode;
use XLite\Core\Mail\Common\TestEmail;
use XLite\Core\Mail\Common\UpgradeSafeMode;
use XLite\Core\Mail\Profile\RecoverPasswordAdmin;
use XLite\Core\Mail\Profile\RecoverPasswordCustomer;

abstract class Scheduler
{
    use SchedulingJobs {
        schedule as scheduleJob;
    }

    public static function schedule(AMail $mail)
    {
        $result = false;

        if ($mail::isEnabled()) {
            $result = static::isMailQueueExcluded($mail)
                ? $mail->send()
                : static::doSchedule($mail);
        }

        return $result;
    }

    /**
     * @param AMail $mail
     */
    protected static function doSchedule(AMail $mail)
    {
        $job = new SendMail($mail);

        static::scheduleJob($job);
    }

    /**
     * @param AMail $mail
     *
     * @return bool
     */
    protected static function isMailQueueExcluded(AMail $mail)
    {
        return $mail instanceof TestEmail
            || $mail instanceof RecoverPasswordCustomer
            || $mail instanceof RecoverPasswordAdmin
            || $mail instanceof SafeMode
            || $mail instanceof UpgradeSafeMode;
    }
}
