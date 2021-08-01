<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job;

use XLite\Core\Job\StepsProvider\StepsProviderInterface;
use XLite\Core\Queue\Message\StateReportMessage;

class StepsJob extends JobAbstract
{
    /**
     * @var int
     */
    private $position;
    /**
     * @var int
     */
    private $stepSize;
    /**
     * @var StepsProviderInterface
     */
    private $stepsProvider;

    function __construct(StepsProviderInterface $stepsProvider, $stepSize, $position = 0, $id = null)
    {
        parent::__construct($id);

        $this->stepsProvider = $stepsProvider;
        $this->position = $position;
        $this->stepSize = $stepSize;
    }

    public function handle()
    {
        if ($this->position === 0) {
            $this->markAsStarted();
        }

        $batch = $this->stepsProvider->getBatch($this->position, $this->stepSize);
        $this->position += count($batch);

        $batch = array_filter($batch);

        if ($this->stepsProvider->isValid() && $this->position <= $this->stepsProvider->getCount()) {
            $this->scheduleNextBatch();
        } else {
            $this->markAsFinished();
        }

        /** @var Job $step */
        foreach ($batch as $step) {
            $step->handle();
        }

        $this->notifyState();
    }

    public function notifyState()
    {
        $manager = \XLite\Core\Queue\Scheduler\SchedulerService::createDefaultStateReportScheduler();

        $manager->schedule(new StateReportMessage(
            $this->getId(),
            [
               'progress'   => $this->getProgress(),
            ]
        ));
    }

    protected function scheduleNextBatch()
    {
        $scheduler = \XLite\Core\Queue\Scheduler\SchedulerService::createOnlineJobScheduler();

        $scheduler->schedule(new \XLite\Core\Job\StepsJob(
            $this->stepsProvider,
            $this->stepSize,
            $this->position,
            $this->getId()
        ));
    }

    public function getProgress()
    {
        if (!$this->stepsProvider->getCount()) {
            return 100;
        }

        return ($this->position / $this->stepsProvider->getCount()) * 100;
    }

    /**
     * @return string
     */
    public function getPreferredQueue()
    {
        return \XLite\Core\Queue\Driver::QUEUE_HIGH_PRIORITY;
    }
}
