<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Controller\Admin;

use XLite\Core\Database;
use XLite\Core\EventTask;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XLite\Module\XC\GoogleFeed\Logic\Feed\Generator;

/**
 * Google feed generation & settings
 */
class GoogleFeed extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Google product feed');
    }

    /**
     * Check - generation process is not-finished or not
     *
     * @return boolean
     */
    public function isFeedGenerationNotFinished()
    {
        $eventName = Generator::getEventName();
        $state = Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
        && in_array(
            $state['state'],
            array(EventTask::STATE_STANDBY, EventTask::STATE_IN_PROGRESS)
        )
        && !Database::getRepo('XLite\Model\TmpVar')->getVar($this->getGenerationCancelFlagVarName());
    }

    /**
     * Check - generation process is finished or not
     *
     * @return boolean
     */
    public function isGenerationFinished()
    {
        return !$this->isFeedGenerationNotFinished();
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getGenerationCancelFlagVarName()
    {
        return Generator::getCancelFlagVarName();
    }

    /**
     * Manually generate sitemap
     *
     * @return void
     */
    protected function doActionGenerate()
    {
        if ($this->isGenerationFinished()) {
            Generator::run([]);
        }

        $this->setReturnURL(
            $this->buildURL('google_feed')
        );
    }

    /**
     * Update module settings
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('update');

        \XLite\Module\XC\GoogleFeed\Core\Task\FeedUpdater::setRenewalPeriod(
            \XLite\Core\Config::getInstance()->XC->GoogleFeed->renewal_frequency
        );
    }

    /**
     * getModelFormClass
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Settings';
    }

    /**
     * Cancel
     *
     * @return void
     */
    protected function doActionFeedGenerationCancel()
    {
        Generator::cancel();
        TopMessage::addWarning('Feed generation has been stopped.');

        $this->setReturnURL(
            $this->buildURL('google_feed')
        );
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $request = Request::getInstance();

        if ($request->generation_completed) {
            TopMessage::addInfo('Feed generation has been completed successfully.');

            $this->setReturnURL(
                $this->buildURL('google_feed')
            );

        } elseif ($request->generation_failed) {
            TopMessage::addError('Feed generation has been stopped.');

            $this->setReturnURL(
                $this->buildURL('google_feed')
            );
        }
    }

    /**
     * Returns shipping options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->executeCachedRuntime(function () {
            return Database::getRepo('\XLite\Model\Config')
                ->findByCategoryAndVisible($this->getOptionsCategory());
        });
    }

    /**
     * Get options category
     *
     * @return string
     */
    protected function getOptionsCategory()
    {
        return 'XC\GoogleFeed';
    }
}
