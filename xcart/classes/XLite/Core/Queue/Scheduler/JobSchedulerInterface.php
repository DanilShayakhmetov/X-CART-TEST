<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Scheduler;

use XLite\Core\Job\Job;

/**
 * Interface for JobScheduler implementations
 */
interface JobSchedulerInterface
{
    /**
     * @param Job  $job
     * @param null $queue
     */
    public function schedule(Job $job, $queue = null);

    public function cancelJob($id);
}
