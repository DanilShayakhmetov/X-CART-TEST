<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue;

use XLite\Core\Job\Job;
use XLite\Core\Queue\Message\JobMessage;

class JobMessageBuilder
{
    /**
     * @param Job $job
     *
     * @return string
     */
    public function build(Job $job)
    {
        return new JobMessage($job);
    }
}
