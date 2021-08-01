<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Worker;

use XLite\Core\Job\State\JobStateInterface;
use XLite\Core\Job\State\Registry\StateRegistryInterface;
use XLite\Core\Queue\Message\StateReportMessage;

/**
 * Class StateReportWorker
 *
 * Note:
 *   This worker is a part of schedulingSide
 */
class StateReportWorker
{
    /**
     * @var StateRegistryInterface
     */
    private $stateRegistry;

    function __construct(StateRegistryInterface $stateRegistry)
    {
        $this->stateRegistry = $stateRegistry;
    }

    public function stateReport(StateReportMessage $message)
    {
        $jobId = $message->getJobId();
        $progress = $message->getProgress();

        $this->stateRegistry->process($jobId, function ($id, JobStateInterface $jobState) use ($message, $progress) {
            $timestamp = $jobState->getData('timestamp');

            if ($message->getTimestamp() > $timestamp) {
                $jobState->setProgress($progress);
                $jobState->setData('timestamp', $message->getTimestamp());
            }

            return $jobState;
        });
    }

}
