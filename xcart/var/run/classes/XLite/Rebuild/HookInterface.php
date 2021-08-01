<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Rebuild;

/**
 * Interface HookInterface
 * @package XLite\Rebuild
 * @see https://devs.x-cart.com/misc/upgrade_hooks.html
 */
interface HookInterface
{
    /**
     * Context provider
     * @param HookContext $context
     */
    public function init($context);

    /**
     * Is called upon the upgrade process
     *
     * @param array $state Array of ['position' => integer, 'count' => integer]
     * @param HookContext $context
     * @return array|null Array of modified state [$position, $count] or null, if the hook finished the work
     */
    public function run($state, $context);
}
