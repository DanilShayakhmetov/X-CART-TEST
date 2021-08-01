<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Consumer;

use Bernard\Consumer;
use Bernard\Queue;

class CheckingInnerConsumer extends Consumer
{
    /**
     * {@inheritdoc}
     */
    public function tick(Queue $queue, array $options = [])
    {
        if (function_exists('pcntl_signal_dispatch')) {
            pcntl_signal_dispatch();
        }

        $this->configure($options);

        if ($this->shutdown) {
            return false;
        }

        if (microtime(true) > $this->options['max-runtime']) {
            return false;
        }

        if ($this->pause) {
            return true;
        }

        try {
            $envelopes = $queue->peek(0, 1);
        } catch (\Assert\InvalidArgumentException $e) {
            $envelopes = [];

            $message = sprintf(
                "There is an incorrect message in queue, peek failed with exception: %s",
                $e->getMessage()
            );
            \XLite\Logger::logCustom('queue', $message);

            try {
                $queue->dequeue();
            } catch (\Assert\InvalidArgumentException $e) {
                $message = sprintf(
                    "Failed to remove invalid message from queue: %s",
                    $e->getMessage()
                );
                \XLite\Logger::logCustom('queue', $message);
            }
        }

        $envelope = reset($envelopes);
        if (!$envelope || !$this->shouldProcess($envelope)) {
            return !$this->options['stop-when-empty'];
        }

        return parent::tick($queue, $options);
    }

    /**
     * @param $envelope
     *
     * @return bool
     */
    protected function shouldProcess($envelope)
    {
        if (! $this->router instanceof CheckingRouter) {
            return true;
        }

        try {
            // for 5.3 support where a function name is not a callable
            return call_user_func($this->router->mapChecker($envelope), $envelope->getMessage());

        } catch (CheckingReceiverNotFoundException $error) {
            // Without checking receiver we are assuming that we should process job anyway
            return true;
        }
    }
}
