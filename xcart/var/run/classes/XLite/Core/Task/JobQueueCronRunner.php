<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Task;

use XLite\Core\Queue\Consumer\ConsumerService;

/**
 * JobQueueCronRunner
 */
class JobQueueCronRunner extends \XLite\Core\Task\Base\Periodic
{
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Scheduled jobs';
    }

    /**
     * Run step
     *
     * @return void
     */
    protected function runStep()
    {
        $queues = \XLite\Core\Queue\Driver::getInstance()->listQueues();

        $continue = true;

        $callback = function () use ($continue) {
            $continue = false;
        };

        foreach ($queues as $queue) {
            if (!$continue) {
                break;
            }

            $manager = ConsumerService::createDefaultConsumerForJobMessage('cron_runner_' . uniqid());
            $manager->AddListener(\Bernard\BernardEvents::INVOKE, $callback);
            $manager->consume($queue, [
                'stop-when-empty' => true,
                'stop-on-error' => true,
                'max-runtime'   => 60,
            ]);
        }
    }

    /**
     * Get period (seconds)
     *
     * @return integer
     */
    protected function getPeriod()
    {
        return 0;
    }

}
