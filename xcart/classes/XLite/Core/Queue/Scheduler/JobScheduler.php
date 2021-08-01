<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Scheduler;

use Bernard\EventListener\ErrorLogSubscriber;
use Bernard\EventListener\FailureSubscriber;
use Bernard\Producer;
use Bernard\QueueFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;
use XLite\Core\Job\Job;
use XLite\Core\Job\State\JobStateInterface;
use XLite\Core\Job\State\StateRegistryFactory;
use XLite\Core\Queue\JobMessageBuilder;

class JobScheduler implements JobSchedulerInterface
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
     * @var JobQueueResolverInterface
     */
    private $jobQueueResolver;

    /**
     * JobExecutor constructor.
     *
     * @param QueueFactory              $queueFactory
     * @param JobQueueResolverInterface $jobQueueResolver
     *
     * @internal param $defaultQueue
     */
    public function __construct(QueueFactory $queueFactory, JobQueueResolverInterface $jobQueueResolver)
    {
        $this->factory = $queueFactory;
        $this->jobQueueResolver = $jobQueueResolver;

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new ErrorLogSubscriber());

        $this->producer = new Producer($this->factory, $dispatcher);
        $dispatcher->addSubscriber(new FailureSubscriber($this->producer));
    }

    /**
     * @param Job  $job
     * @param null $queue
     */
    public function schedule(Job $job, $queue = null)
    {
        $name = $job->getName();
        StateRegistryFactory::initiate(
            $job->getId(),
            function(JobStateInterface $state) use ($name) {
                $state->setData('human_name', $name);

                return $state;
            }
        );

        $builder = new JobMessageBuilder();
        $queue = $queue ?: $this->jobQueueResolver->resolve($job);

        $this->producer->produce(
            $builder->build($job),
            $queue
        );
    }

    /**
     * @param Job  $job
     * @param null $queue
     *
     * TODO Implement this via messaging or another mechanism.
     *     Now method changing state directly, its only work because we have
     *     schedulingSide and executingSide on the same machine
     */
    public function cancelJob($id)
    {
        StateRegistryFactory::createStateRegistry()->process(
            $id,
            function($id, JobStateInterface $jobState) {
                $jobState->setCancelled(true);
                return $jobState;
            }
        );
    }
}
