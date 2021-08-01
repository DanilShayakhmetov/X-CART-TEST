<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Worker;

use Bernard\Message\DefaultMessage;
use XLite\Core\Job\State\Registry\StateRegistryInterface;

/**
 * Class CancelWorker
 * TODO not implemented
 */
class CancelWorker
{
    /**
     * @var StateRegistryInterface
     */
    private $stateRegistry;

    function __construct(StateRegistryInterface $stateRegistry)
    {
        $this->stateRegistry = $stateRegistry;
    }

    public function cancel(DefaultMessage $message)
    {
        $jobId = $message['id'];

        $jobState = $this->stateRegistry->get($jobId);
        if ($jobState && !$jobState->isCancelled()) {
            $jobState->setCancelled(true);
            $this->stateRegistry->set($jobId, $jobState);
        }
    }

}
