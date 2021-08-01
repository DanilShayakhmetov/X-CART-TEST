<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Job;

use XLite\Core\Job\State\StateRegistryFactory;

/**
 * Class Progress
 */
class Progress extends \XLite\View\AView
{
    protected function isVisible()
    {
        //TODO remove when job with steps will be ready
        return false;
    }

    const PARAMS_JOB_ID = 'jobId';

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAMS_JOB_ID => new \XLite\Model\WidgetParam\TypeInt('Job ID', 0),
        );
    }

    /**
     * @return mixed
     */
    public function getJobId()
    {
        return $this->getParam(static::PARAMS_JOB_ID)
            ?: \XLite\Core\Request::getInstance()->id;
    }

    /**
     * @return mixed
     */
    public function getJobName()
    {
        $state = $this->getState();

        return $state
            ? $state->getData('human_name')
            : 'Job #' . $this->getJobId();
    }

    /**
     * @inheritDoc
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                'job/runner.js',
                'job/controller.js',
            ]
        );
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'job/style.less';
        return $list;
    }

    public function getInitialProgress()
    {
        $state = $this->getState();

        $progress = 0;
        if ($state) {
            $progress = $state->getProgress();
        }

        return intval($progress);
    }

    protected function getState()
    {
        $stateRegistry = StateRegistryFactory::createStateRegistry();

        return $stateRegistry->get($this->getJobId());
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'job/progress.twig';
    }
}
