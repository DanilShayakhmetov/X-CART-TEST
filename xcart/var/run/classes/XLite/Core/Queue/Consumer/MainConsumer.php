<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Consumer;

use Bernard\EventListener\ErrorLogSubscriber;
use Bernard\EventListener\FailureSubscriber;
use Bernard\QueueFactory;
use Bernard\Router;
use Bernard\Producer;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MainConsumer
{
    /**
     * @var QueueFactory
     */
    protected $factory;

    /**
     * @var Producer
     */
    protected $producer;

    /**
     * @var QueueFactory
     */
    protected $dispatcher;

    /**
     * JobExecutor constructor.
     *
     * @param QueueFactory $queueFactory
     * @param Router $router
     */
    public function __construct(QueueFactory $queueFactory, Router $router)
    {
        $this->factory = $queueFactory;

        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber(new ErrorLogSubscriber());

        $this->producer = new Producer(
            $this->factory,
            $this->dispatcher
        );
        $this->dispatcher->addSubscriber(new FailureSubscriber($this->producer));

        $this->consumer = new CheckingInnerConsumer($router, $this->dispatcher);
    }

    public function consume($queue, $options = [])
    {
        $jobQueue = $this->factory->create($queue);

        $this->consumer->consume($jobQueue, $options);
    }

    /**
     * @param string $name Event name
     * @param callable $callback
     */
    public function AddListener($name, callable $callback)
    {
        $this->dispatcher->addListener($name, $callback);
    }
}
