<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Scheduler;

use Bernard\EventListener\ErrorLogSubscriber;
use Bernard\EventListener\FailureSubscriber;
use Bernard\Message;
use Bernard\Producer;
use Bernard\QueueFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;
use XLite\Core\Job\Job;

class MessageScheduler
{
    /**
     * @var QueueFactory
     */
    protected $factory;

    /**
     * @var string
     */
    protected $defaultQueue;

    /**
     * JobExecutor constructor.
     *
     * @param QueueFactory $queueFactory
     * @param              $defaultQueue
     */
    function __construct(QueueFactory $queueFactory, $defaultQueue)
    {
        $this->factory = $queueFactory;
        $this->defaultQueue = $defaultQueue;

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new ErrorLogSubscriber());
        $dispatcher->addSubscriber(new FailureSubscriber($this->factory));

        $this->producer = new Producer($this->factory, $dispatcher);
    }

    /**
     * @param Job  $job
     * @param null $queue
     */
    public function schedule(Message $message, $queue = null)
    {
        $queue = $queue ?: $this->defaultQueue;

        $this->producer->produce($message, $queue);
    }
}
