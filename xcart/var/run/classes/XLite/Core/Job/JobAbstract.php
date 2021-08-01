<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job;


use XLite\Core\Job\State\JobStateInterface;

abstract class JobAbstract implements Job, StateRegistryAwareInterface
{
    use StateRegistryAwareTrait;

    /**
     * @var
     */
    protected $id;

    public function __construct($id = null)
    {
        $this->id = $id ?: uniqid('job');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return 'Job #' . $this->id;
    }

    /**
     * @return mixed
     */
    public function getPreferredQueue()
    {
        return 'lowPriority';
    }

    public function markAsFinished()
    {
        $this->getStateRegistry()->process($this->getId(), function ($id, JobStateInterface $state) {
            $state->setFinished(true);
            $state->setProgress(100);
        });
    }

    public function markAsStarted()
    {
        $this->getStateRegistry()->process($this->getId(), function ($id, JobStateInterface $state) {
            $state->setStartedAt(time());
        });
    }
}
