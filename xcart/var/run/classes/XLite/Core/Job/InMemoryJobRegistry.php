<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job;

/**
 * Simple wrapper around SqlQueue to operate on stored jobs
 *
 * @package XLite\Core\Job
 */
class InMemoryJobRegistry extends \XLite\Base\Singleton
{
    /**
     * @var \SplQueue
     */
    protected $queue;

    public function __construct()
    {
        parent::__construct();

        $this->queue = new \SplQueue();
        $this->queue->setIteratorMode(\SplQueue::IT_MODE_DELETE);
    }

    /**
     * @param Job $job
     */
    public function queue(Job $job)
    {
        $this->queue->enqueue($job);
    }

    /**
     * @return bool
     */
    public function hasJobs()
    {
        return $this->queue->count() > 0;
    }

    /**
     * @return Job
     */
    public function consume()
    {
        return $this->queue->dequeue();
    }
}
