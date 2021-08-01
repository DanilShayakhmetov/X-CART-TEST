<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request;

use XLite\Core\Job\SchedulingJobs;
use XLite\Module\XC\MailChimp\Core\Job\ExecuteMailChimpRequest;

abstract class Scheduler
{
    use SchedulingJobs {
        schedule as scheduleJob;
    }

    public static function schedule(IMailChimpRequest $request)
    {
        return static::isScheduleAllowed($request)
            ? static::doSchedule($request)
            : $request->execute();

    }

    /**
     * @param IMailChimpRequest $request
     */
    protected static function doSchedule(IMailChimpRequest $request)
    {
        $job = new ExecuteMailChimpRequest($request);

        static::scheduleJob($job);
    }

    /**
     * @param IMailChimpRequest $request
     *
     * @return bool
     */
    protected static function isScheduleAllowed(IMailChimpRequest $request): bool
    {
        return true;
    }
}
