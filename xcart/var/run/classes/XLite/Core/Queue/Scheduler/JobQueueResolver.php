<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Scheduler;


use XLite\Core\Job\Job;

class JobQueueResolver implements JobQueueResolverInterface
{
    /**
     * @param Job $job
     *
     * @return mixed
     */
    public function resolve(Job $job) {
        return $job->getPreferredQueue();
    }
}
