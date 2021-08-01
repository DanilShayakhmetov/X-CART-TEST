<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Console;

/**
 * Consumer
 *
 * TODO Rethink it
 */
class Consumer extends \XLite\Controller\Console\AConsole
{
    protected function getQueueName()
    {
        return \XLite\Core\Request::getInstance()->queue
            ?: \XLite\Core\Queue\Driver::QUEUE_HIGH_PRIORITY;
    }

    /**
     */
    protected function doActionConsume()
    {
        $manager = \XLite\Core\Queue\Consumer\ConsumerService::createDefaultConsumerForJobMessage('daemon_runner_' . uniqid());
        $manager->consume($this->getQueueName());
    }

    /**
     */
    protected function doActionConsumeAll()
    {
        $queues = \XLite\Core\Queue\Driver::getInstance()->listQueues();

        $continueStep = true;

        $callback = function () use ($continueStep) {
            $continueStep = false;
        };

        while (true) {
            $continueStep = true;
            foreach ($queues as $queue) {
                if (!$continueStep) {
                    break;
                }

                $manager = \XLite\Core\Queue\Consumer\ConsumerService::createDefaultConsumerForJobMessage('daemon_runner_' . uniqid());
                $manager->AddListener(\Bernard\BernardEvents::INVOKE, $callback);
                $manager->consume($queue, [
                    'max-messages' => 1,
                    'stop-when-empty' => true
                ]);
            }
        }
    }
}

