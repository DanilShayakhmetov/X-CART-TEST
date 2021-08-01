<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue;

use XLite\Core\Queue\Driver\FlatFileDriver;

class Driver extends \XLite\Base\Singleton implements \Bernard\Driver
{
    const QUEUE_HIGH_PRIORITY = 'highPriority';
    const QUEUE_LOW_PRIORITY  = 'lowPriority';
    const QUEUE_STATE_REPORT  = 'stateReport';

    /**
     * @var \Bernard\Driver
     */
    protected $driver;

    /**
     * @return \Bernard\Driver
     */
    protected function create()
    {
        $dir = LC_DIR_DATA . 'queue';

        if (!\Includes\Utils\FileManager::isExists($dir)) {
            \Includes\Utils\FileManager::mkdirRecursive($dir, 0777);
        }

        return new FlatFileDriver($dir, 0777);
    }

    /**
     * @return \Bernard\Driver
     */
    protected function get()
    {
        if (!$this->driver) {
            $this->driver = $this->create();
        }

        return $this->driver;
    }

    /**
     * Return sorted by weight queues list
     *
     * @return array
     */
    public function listQueues()
    {
        $queues = $this->get()->listQueues();

        $queues = array_merge($queues, array_keys($this->getQueuesPrioritiesList()));

        usort($queues, function ($q1, $q2) {
            $w1 = $this->getQueueWeight($q1);
            $w2 = $this->getQueueWeight($q2);

            return $w1 === $w2 ? 0 : ($w1 > $w2 ? -1 : 1);
        });

        return $queues;
    }

    /**
     * Returns queue weight(if not defined return 0)
     *
     * @param string $queueName
     * @return int
     */
    public function getQueueWeight($queueName)
    {
        return isset($this->getQueuesPrioritiesList()[$queueName])
            ? $this->getQueuesPrioritiesList()[$queueName]
            : 0;
    }

    /**
     * Returns queues weights with queues as array keys
     *
     * @return array
     */
    public function getQueuesPrioritiesList()
    {
        return [
            static::QUEUE_STATE_REPORT  => 2000,
            static::QUEUE_HIGH_PRIORITY => 1000,
            static::QUEUE_LOW_PRIORITY  => 100,
        ];
    }

    // {{{ \Bernard\Driver

    /**
     * @inheritdoc
     */
    public function createQueue($queueName)
    {
        $this->get()->createQueue($queueName);
    }

    /**
     * @inheritdoc
     */
    public function countMessages($queueName)
    {
        return $this->get()->countMessages($queueName);
    }

    /**
     * @inheritdoc
     */
    public function pushMessage($queueName, $message)
    {
        $this->get()->pushMessage($queueName, $message);
    }

    /**
     * @inheritdoc
     */
    public function popMessage($queueName, $duration = 5)
    {
        return $this->get()->popMessage($queueName, $duration);
    }

    /**
     * @inheritdoc
     */
    public function acknowledgeMessage($queueName, $receipt)
    {
        $this->get()->acknowledgeMessage($queueName, $receipt);
    }

    /**
     * @inheritdoc
     */
    public function peekQueue($queueName, $index = 0, $limit = 20)
    {
        return $this->get()->peekQueue($queueName, $index, $limit);
    }

    /**
     * @inheritdoc
     */
    public function removeQueue($queueName)
    {
        $this->get()->removeQueue($queueName);
    }

    /**
     * @inheritdoc
     */
    public function info()
    {
        return $this->get()->info();
    }

    // }}}
}
