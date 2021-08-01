<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Rebuild;

class Hook implements HookInterface
{
    protected $onRun;
    protected $onInit;

    /**
     * @var HookContext
     */
    protected $context;

    /**
     * Hook constructor.
     *
     * @param callable $runFn
     * @param callable $initFn
     */
    public function __construct($runFn, $initFn = null)
    {
        $this->onRun  = $runFn;
        $this->onInit = $initFn;
    }

    /**
     * @param HookContext $context
     */
    public function init($context)
    {
        $this->context = $context;

        if (is_callable($this->onInit)) {
            $closure = \Closure::bind($this->onInit, $this);
            $closure($context);
        }
    }

    /**
     * Is called upon the upgrade process
     *
     * @param array       $state Array of ['position' => integer, 'count' => integer]
     * @param HookContext $context
     *
     * @return array|null Array of modified state [$position, $count] or null, if the hook finished the work
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function run($state, $context)
    {
        $this->context = $context;

        if (is_callable($this->onRun)) {
            $closure = \Closure::bind($this->onRun, $this);
            $result  = $closure($state);

            $this->context->getEM()->flush();
            $this->context->getEM()->clear();

            return $result;
        }

        return null;
    }
}
