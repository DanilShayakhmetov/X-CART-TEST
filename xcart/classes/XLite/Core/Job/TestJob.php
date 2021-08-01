<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job;

/**
 * Class TestJob
 * TODO remove me
 */
class TestJob extends JobAbstract
{
    /**
     * @var
     */
    private $msg;

    function __construct($msg)
    {
        parent::__construct();

        $this->msg = $msg;
    }

    public function handle()
    {
        $this->markAsStarted();
        var_dump('test from testJob| '. $this->msg);

        \XLite\Logger::logCustom('test_job', $this->msg);
        usleep(100 * 1000);

        $this->markAsFinished();
    }
}
