<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job\State;


use XLite\Core\Job\State\Registry\DatabaseDrivenRegistry;
use XLite\Core\Job\State\Registry\StateRegistryInterface;

/**
 * Class StateRegistryFactory
 *
 * =========== N.B. ===========
 * In current implementation this is used in both executingSide and schedulingSide,
 * because they both are running in one environment, and both have access to the same database
 * But there is possibility that they will be implemented differently
 */
class StateRegistryFactory
{
    /**
     * @return StateRegistryInterface
     */
    public static function createStateRegistry()
    {
        return new DatabaseDrivenRegistry();
    }

    public static function initiate($jobId, $callback = null)
    {
        /** @var StateRegistryInterface $stateRegistry */
        $stateRegistry = StateRegistryFactory::createStateRegistry();

        if (!$stateRegistry->get($jobId)) {
            $state = StateFactory::create();
            if (is_callable($callback)) {
                $state = $callback($state);
            }
            $stateRegistry->set($jobId, $state);
        }
    }
}
