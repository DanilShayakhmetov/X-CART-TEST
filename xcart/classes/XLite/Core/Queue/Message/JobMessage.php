<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Message;

use XLite\Core\Job\Job;

/**
 * Class JobMessage
 * @package XLite\Core\Queue\Message
 */
class JobMessage extends AbstractMessage
{
    protected $name;
    protected $job;

    /**
     * @param Job $job
     * @param string $name
     */
    public function __construct(Job $job, $name = 'XCartJob')
    {
        parent::__construct();

        $this->name = preg_replace('/(^([0-9]+))|([^[:alnum:]\-_+])/i', '', $name);
        $this->job = $job;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }
}
