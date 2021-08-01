<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Worker;

use XLite\Core\Job\State\JobStateInterface;
use XLite\Core\Job\State\Registry\StateRegistryInterface;
use XLite\Core\Job\StateRegistryAwareInterface;
use XLite\Core\Queue\Message\JobMessage;

class JobWorker
{
    /**
     * @var StateRegistryInterface
     */
    private $stateRegistry;

    /**
     * @var string
     */
    private $runnerName;

    /**
     * @var float
     */
    private $pickAfter;


    /**
     * JobWorker constructor.
     *
     * @param StateRegistryInterface $stateRegistry
     * @param                        $runnerName
     * @param float                  $pickAfter
     */
    function __construct(StateRegistryInterface $stateRegistry, $runnerName, $pickAfter = 30.0)
    {
        $this->stateRegistry    = $stateRegistry;
        $this->runnerName       = $runnerName;
        $this->pickAfter        = floatval($pickAfter);
    }

    /**
     * @param JobMessage $message
     *
     * @throws \Exception
     */
    public function xCartJob(JobMessage $message)
    {
        $job = $message->getJob();

        $jobState = $this->stateRegistry->process($job->getId(), function($id, JobStateInterface $jobState) {
            $jobState->setData('lastRunnerName', $this->runnerName);
            $jobState->setData('pickedAt', microtime(true));
            echo sprintf('Job #%s started via %s'.PHP_EOL, $id, $this->runnerName);

            return $jobState;
        });

        if ($job instanceof StateRegistryAwareInterface) {
            $job->setStateRegistry($this->stateRegistry);
        }

        if (!$jobState->isCancelled()) {
            $job->handle();
        } else {
            echo sprintf('Job #%s is cancelled'.PHP_EOL, $job->getId());
        }
    }


    /**
     * @param JobMessage $message
     *
     * @return bool
     */
    public function xCartJobCheck(JobMessage $message)
    {
        $job = $message->getJob();
        $jobId = $job->getId();

        $jobState = $this->stateRegistry->get($jobId);

        if (!$jobState) {
            return false;
        }

        $pickedAt   = $jobState->getData('pickedAt');
        $lastRunner = $jobState->getData('lastRunnerName');
        if ($lastRunner
            && $this->runnerName !== $lastRunner
            && $pickedAt
            && (microtime(true) - $pickedAt) <= $this->pickAfter
        ) {
            echo sprintf('Job #%s will not be picked. Yet. Locked by %s'.PHP_EOL, $jobId, $lastRunner);
            return false;
        }

        return true;
    }
}
