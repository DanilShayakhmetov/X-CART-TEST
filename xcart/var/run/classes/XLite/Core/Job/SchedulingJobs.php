<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job;


trait SchedulingJobs
{
    protected static function getScheduler()
    {
        return \XLite\Core\Queue\Scheduler\SchedulerService::createDefaultJobScheduler();
    }

    /**
     * @param Job  $job
     * @param null $queue
     */
    protected static function schedule(Job $job, $queue = null)
    {
        if (!static::getScheduler()) {
            return;
        }

        static::getScheduler()->schedule($job, $queue);
    }
}
