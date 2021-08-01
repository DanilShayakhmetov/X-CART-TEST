<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Job\State\StateRegistryFactory;

class Jobs extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Set if the form id is needed to make an actions
     * Form class uses this method to check if the form id should be added
     *
     * @return boolean
     */
    public static function needFormId()
    {
        return false;
    }

    public function doActionCancel()
    {
        $this->silent = true;
        $this->setSuppressOutput(true);

        $manager = \XLite\Core\Queue\Scheduler\SchedulerService::createOnlineJobScheduler();
        $manager->cancelJob($this->getJobId());
    }

    public function doActionRun()
    {
        $this->silent = true;
        $this->setSuppressOutput(true);

        ob_start();
        if ($this->shouldConsumeJob() ) {
            $this->consumeJob();
        }
        ob_end_clean();
    }


    protected function consumeJob($runtime = 30)
    {
        $manager = \XLite\Core\Queue\Consumer\ConsumerService::createDefaultConsumerForJobMessage('js_runner_'.uniqid());
        $options = [
            'stop-when-empty'   => true,
            'stop-on-error'     => true,
            'max-runtime'       => $runtime,
        ];

        $manager->consume(\XLite\Core\Queue\Driver::QUEUE_HIGH_PRIORITY, $options);
    }

    protected function shouldConsumeJob()
    {
        return (boolean) \XLite::getInstance()->getOptions([ 'queue', 'jsRunnerForOnlineEnabled' ]);
    }

    public function doActionGetProgress()
    {
        $this->silent = true;
        $this->setSuppressOutput(true);

        if ($this->shouldConsumeProgress()) {
            $this->consumeProgress();
        }

        $this->displayJSON($this->getJobProgressData($this->getJobId()));
    }


    public function doActionGetProgressMultiple()
    {
        $this->silent = true;
        $this->setSuppressOutput(true);

        if ($this->shouldConsumeProgress()) {
            $this->consumeProgress();
        }

        $ids = \XLite\Core\Request::getInstance()->ids;

        if (!$ids) {
            $ids = array_map(
                function ($state) {
                    return $state->getId();
                },
                \XLite\Core\Database::getRepo('XLite\Model\Job\State')->getNotFinishedJobs()
            );
        } else {
            $ids = explode(',', $ids);
        }

        $ids = array_filter($ids);

        $progressData = [];
        foreach ($ids as $id) {
            $progressData[$id] = $this->getJobProgressData($id);
        }

        $this->displayJSON($progressData);
    }

    /**
     * @param $id
     *
     * @return array
     */
    protected function getJobProgressData($id)
    {
        $stateRegistry = StateRegistryFactory::createStateRegistry();
        $state = $stateRegistry->get($id);

        if ($state && $state->isCancelled()) {
            $this->headerStatus(400);
            return [ 'error' => 'job_cancelled' ];
        }

        $result = [
            'progress'    => 0,
            'isStarted'   => false,
            'isFinished'  => false
        ];

        if ($state) {
            $result['progress']     = $state->getProgress();
            $result['isStarted']    = $state->isStarted();
            $result['isFinished']   = $state->isFinished();
        }

        return $result;
    }

    public function doActionGetJobs()
    {
        $this->silent = true;
        $this->setSuppressOutput(true);

        $jobs = array_map(
            function ($state) {
                return $state->getId();
            },
            \XLite\Core\Database::getRepo('XLite\Model\Job\State')->getNotFinishedJobs()
        );

        $this->displayJSON([
            'jobs'  => array_filter($jobs),
        ]);
    }

    protected function consumeProgress()
    {
        $manager = \XLite\Core\Queue\Consumer\ConsumerService::createDefaultConsumerForStateReportMessage();
        $options = [
            'stop-when-empty'   => true,
            'stop-on-error'     => true,
            'max-runtime'       => 5,
        ];

        $manager->consume('stateReport', $options);
    }

    protected function shouldConsumeProgress()
    {
        return \XLite\Core\Request::getInstance()->consumeProgress !== null
            ? \XLite\Core\Request::getInstance()->consumeProgress
            : true;
    }

    public function getJobId()
    {
        return \XLite\Core\Request::getInstance()->id;
    }
}
