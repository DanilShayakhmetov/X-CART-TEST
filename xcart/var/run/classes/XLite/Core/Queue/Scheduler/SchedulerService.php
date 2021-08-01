<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Scheduler;

use XLite\Core\Queue\Message\EnvelopeNormalizer;
use Bernard\QueueFactory\PersistentFactory;
use Bernard\Serializer;
use Normalt\Normalizer\AggregateNormalizer;
use XLite\Core\Job\State\StateRegistryFactory;
use XLite\Core\Queue\Driver;
use XLite\Core\Queue\Message\JobMessageNormalizer;
use XLite\Core\Queue\Message\StateReportMessageNormalizer;

class SchedulerService
{
    /**
     * @return MessageScheduler
     */
    public static function createDefaultStateReportScheduler($queueName = null)
    {
        $normalizer = new AggregateNormalizer([
            new EnvelopeNormalizer(),
            new StateReportMessageNormalizer(),
        ]);

        $serializer = new Serializer($normalizer);
        $queueName = $queueName ?: 'stateReport';

        $driver = Driver::getInstance();
        $factory = new PersistentFactory($driver, $serializer);

        return new MessageScheduler($factory, $queueName);
    }

    /**
     * @return JobScheduler
     */
    public static function createOnlineJobScheduler($queueName = null)
    {
        $normalizer = new AggregateNormalizer([
            new EnvelopeNormalizer(),
            new JobMessageNormalizer(),
        ]);

        $serializer = new Serializer($normalizer);

        $driver = Driver::getInstance();
        $factory = new PersistentFactory($driver, $serializer);
        $resolver = new JobQueueResolver();

        return new JobScheduler($factory, $resolver);
    }

    /**
 * @param null $queueName
 * @param bool $endOfRequest Force end of request job scheduler instead of job fake scheduler
 * @return JobSchedulerInterface
 */
    public static function createDefaultJobScheduler($queueName = null, $endOfRequest = false)
    {
        if (!static::isSchedulingEnabled()) {
            return static::createFallbackScheduler($queueName, $endOfRequest);
        }

        $normalizer = new AggregateNormalizer([
            new EnvelopeNormalizer(),
            new JobMessageNormalizer(),
        ]);

        $serializer = new Serializer($normalizer);

        $driver = Driver::getInstance();
        $factory = new PersistentFactory($driver, $serializer);
        $resolver = new JobQueueResolver();

        return new JobScheduler($factory, $resolver);
    }

    /**
     * @param null $queueName
     * @param bool $endOfRequest Force end of request job scheduler instead of job fake scheduler
     * @return JobSchedulerInterface
     */
    public static function createFallbackScheduler($queueName = null, $endOfRequest = false)
    {
        $stateRegistry = StateRegistryFactory::createStateRegistry();
        return $endOfRequest
            ? new RequestEndJobScheduler($stateRegistry)
            : new JobFakeScheduler($stateRegistry);
    }

    public static function isSchedulingEnabled()
    {
        return (boolean) \XLite::getInstance()->getOptions([ 'queue', 'backgroundJobsSchedulingEnabled' ]);
    }
}
