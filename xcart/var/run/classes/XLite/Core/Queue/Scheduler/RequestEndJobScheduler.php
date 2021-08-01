<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Scheduler;

use XLite\Core\Job\InMemoryJobRegistry;
use XLite\Core\Job\Job;
use XLite\Core\Job\State\Registry\StateRegistryInterface;
use XLite\Core\Job\StateRegistryAwareInterface;

/**
 * Executes pending jobs in the end of script after ignore_user_abort statement.
 */
class RequestEndJobScheduler implements JobSchedulerInterface
{
    /**
     * @var StateRegistryInterface
     */
    private $registry;

    public function __construct(StateRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param Job  $job
     * @param null $queue
     */
    public function schedule(Job $job, $queue = null)
    {
        if ($job instanceof StateRegistryAwareInterface) {
            $job->setStateRegistry($this->registry);
        }

        InMemoryJobRegistry::getInstance()->queue($job);
    }

    public function cancelJob($id)
    {
        // noop
    }
}
