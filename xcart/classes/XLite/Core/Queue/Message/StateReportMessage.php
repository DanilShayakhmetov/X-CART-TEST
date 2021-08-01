<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Message;

/**
 * Class StateReportMessage
 * @package XLite\Core\Queue\Message
 */
class StateReportMessage extends AbstractMessage
{
    protected $timestamp;
    private $state;
    private $jobId;

    function __construct($jobId, $state)
    {
        parent::__construct();

        $this->name = 'stateReport';
        $defaults = [
            'progress' => 0
        ];

        $this->state = array_replace($defaults, $state);
        $this->timestamp = microtime(true);
        $this->jobId = $jobId;
    }

    public function getProgress()
    {
        return $this->state['progress'];
    }

    /**
     * @return mixed
     */
    public function getJobId()
    {
        return $this->jobId;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }
}
