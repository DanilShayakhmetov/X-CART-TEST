<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Rebuild\Executor\Entry;

use Symfony\Component\Filesystem\Filesystem;
use XLite\Rebuild\HookContext;
use XLite\Rebuild\HookInterface;

class Hook
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var array
     */
    private $state;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var bool
     */
    private $finished;

    /**
     * @var bool
     */
    private $initialized;

    /**
     * @var HookContext
     */
    private $context;

    /**
     * @param string $file
     * @param array  $state
     * @param bool   $initialized
     */
    public function __construct($file, $state, $initialized = false)
    {
        $this->file        = $file;
        $this->state       = is_array($state) ? $state : [];
        $this->fs          = new Filesystem();
        $this->initialized = $initialized;
        $this->context     = new HookContext();
    }

    /**
     * @throws \Exception
     */
    public function process()
    {
        if ($this->finished) {
            return;
        }

        if (!$this->fs->exists($this->file)) {
            throw new \Exception("Cannot find hook file, type: {$this->file}");
        }

        // Set internal flag
        if (!defined('LC_CACHE_BUILDING')) {
            define('LC_CACHE_BUILDING', true);
        }

        if (!defined('LC_USE_CLEAN_URLS')) {
            define('LC_USE_CLEAN_URLS', false);
        }

        \Includes\Utils\Module\Manager::initModules();

        $hook      = require $this->file;
        $arguments = $this->extractArgFromState($this->state);

        if ($hook instanceof HookInterface) {
            if (!$this->initialized) {
                $hook->init($this->context);

                $this->initialized = true;
            }

            $result = $hook->run($arguments, $this->context);

        } elseif (is_callable($hook)) {
            $result = $hook($arguments);

        } else {

            throw new \Exception("Hook is not callable, file: {$this->file}");
        }

        $this->state = $this->processHookResult($result, $this->state);
    }

    /**
     * @param array $result Hook output
     * @param array $state  Previous state
     *
     * @return array New state
     */
    protected function processHookResult($result, $state)
    {
        if (null === $result) {
            $this->finished = true;

            if (isset($state['count'])) {
                $state['position'] = $state['count'];

            } elseif (!isset($state['position'])) {
                $state['position'] = 1;

            } else {
                $state['position']++;
            }
        } else {
            if (is_array($result)
                && count($result) === 2
            ) {
                list($position, $count) = $result;

                $state['position'] = $position;
                $state['count']    = $count;

            } else {
                if (!isset($state['position'])) {
                    $state['position'] = 0;
                }

                $state['position']++;
            }

            $state['arg'] = $result;
        }

        return $state;
    }

    /**
     * @param array $state
     *
     * @return mixed
     */
    protected function extractArgFromState($state)
    {
        return isset($state['arg'])
            ? $state['arg']
            : null;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return bool
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return $this->finished;
    }
}
