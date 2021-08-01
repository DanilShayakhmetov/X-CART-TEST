<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Consumer;

use XLite\Core\Queue\Message\EnvelopeNormalizer;
use Bernard\QueueFactory\PersistentFactory;
use Bernard\Router;
use Bernard\Router\SimpleRouter;
use Bernard\Serializer;
use Normalt\Normalizer\AggregateNormalizer;
use XLite\Base\Singleton;
use XLite\Core\Job\State\StateRegistryFactory;
use XLite\Core\Queue\Driver;
use XLite\Core\Queue\Message\JobMessageNormalizer;
use XLite\Core\Queue\Message\StateReportMessageNormalizer;
use XLite\Core\Queue\Worker\JobWorker;
use XLite\Core\Queue\Worker\StateReportWorker;

/**
 * Class ConsumerService
 * TODO Try me make creating a new type of message/consumer/producer as straightforward as possible
 */
class ConsumerService extends Singleton
{
    public static function createDefaultConsumerForJobMessage($serviceName)
    {
        $stateRegistry = StateRegistryFactory::createStateRegistry();
        $jobWorker     = new JobWorker($stateRegistry, $serviceName);
        $router = new CheckingRouter(array(
            'XCartJob' => $jobWorker,
        ));

        $normalizer = new AggregateNormalizer([
            new EnvelopeNormalizer(),
            new JobMessageNormalizer(),
        ]);

        $serializer = new Serializer($normalizer);

        return static::createConsumerForMessage(
            $router,
            $serializer
        );
    }

    public static function createDefaultConsumerForStateReportMessage()
    {
        $stateRegistry = StateRegistryFactory::createStateRegistry();
        $worker = new StateReportWorker($stateRegistry);

        $router = new SimpleRouter(array(
            'StateReport' => $worker,
        ));

        $normalizer = new AggregateNormalizer([
            new EnvelopeNormalizer(),
            new StateReportMessageNormalizer(),
        ]);

        $serializer = new Serializer($normalizer);

        return static::createConsumerForMessage(
            $router,
            $serializer
        );
    }

    /**
     * @param Router $router
     * @param        $serializer
     * @param string $queueName
     *
     * @return MainConsumer
     */
    public static function createConsumerForMessage(Router $router, $serializer)
    {
        $driver = Driver::getInstance();
        $factory = new PersistentFactory($driver, $serializer);

        return new MainConsumer(
            $factory,
            $router
        );
    }
}
