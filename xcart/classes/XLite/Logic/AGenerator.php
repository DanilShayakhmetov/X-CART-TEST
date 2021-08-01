<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic;

use XLite\Core\Database;
use XLite\Core\Lock\FileLock;

/**
 * Abstract generator
 */
abstract class AGenerator extends \XLite\Base implements \SeekableIterator, \Countable, \XLite\Logic\GeneratorInterface
{
    /**
     * Default process tick duration
     */
    const DEFAULT_TICK_DURATION = 0.5;

    /**
     * Options
     *
     * @var \ArrayObject
     */
    protected $options;

    /**
     * Generator instance
     *
     * @var AGenerator
     */
    protected static $instance;

    /**
     * Steps (cache)
     *
     * @var array
     */
    protected $steps;

    /**
     * Current step index
     *
     * @var integer
     */
    protected $currentStep;

    /**
     * Count (cached)
     *
     * @var integer
     */
    protected $countCache;

    /**
     * Returns generator if it is initialised or FALSE otherwise
     *
     * @return static|boolean
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            $state = Database::getRepo('XLite\Model\TmpVar')->getEventState(static::getEventName());
            static::$instance = ($state && isset($state['options']))
                ? new static($state['options'])
                : false;
        }

        return static::$instance;
    }

    /**
     * Run
     *
     * @param array $options Options
     *
     * @return void
     */
    public static function run(array $options)
    {
        Database::getRepo('XLite\Model\TmpVar')->setVar(static::getCancelFlagVarName(), false);
        Database::getRepo('XLite\Model\TmpVar')->initializeEventState(
            static::getEventName(),
            ['options' => $options]
        );
        call_user_func(['XLite\Core\EventTask', static::getEventName()]);
    }

    /**
     * Cancel
     *
     * @return void
     */
    public static function cancel()
    {
        Database::getRepo('XLite\Model\TmpVar')->setVar(static::getCancelFlagVarName(), true);
        Database::getRepo('XLite\Model\TmpVar')->removeEventState(static::getEventName());
    }

    /**
     * Constructor
     *
     * @param array $options Options OPTIONAL
     */
    public function __construct(array $options = [])
    {
        $this->options = [
                'include'  => isset($options['include']) ? $options['include'] : [],
                'position' => isset($options['position']) ? intval($options['position']) + 1 : 0,
                'errors'   => isset($options['errors']) ? $options['errors'] : [],
                'warnings' => isset($options['warnings']) ? $options['warnings'] : [],
                'time'     => isset($options['time']) ? intval($options['time']) : 0,
            ] + $options;

        $this->options = new \ArrayObject($this->options, \ArrayObject::ARRAY_AS_PROPS);

        if (0 == $this->getOptions()->position) {
            $this->initialize();
        }
    }

    /**
     * Get options
     *
     * @return \ArrayObject
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $options \ArrayObject
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        Database::getRepo('XLite\Model\TmpVar')->setVar(
            static::getTickDurationVarName(),
            $this->count() ? round($this->getOptions()->time / $this->count(), 3) : 0
        );

        foreach ($this->getSteps() as $step) {
            $step->finalize();
        }
    }

    /**
     * Get time remain
     *
     * @return integer
     */
    public function getTimeRemain()
    {
        return $this->getTickDuration() * ($this->count() - $this->getOptions()->position);
    }

    /**
     * Get process tick duration
     *
     * @return float
     */
    public function getTickDuration()
    {
        $result = null;
        if ($this->getOptions()->time && 1 < $this->getOptions()->position) {
            $result = $this->getOptions()->time / $this->getOptions()->position;

        } else {
            $tick = Database::getRepo('XLite\Model\TmpVar')
                ->getVar(static::getTickDurationVarName());
            if ($tick) {
                $result = $tick;
            }
        }

        return $result
            ? ($result > 0.1 ? ceil($result * 1000) / 1000 : $result)
            : static::DEFAULT_TICK_DURATION;
    }

    /**
     * Initialize
     *
     * @return void
     */
    protected function initialize()
    {
    }

    // {{{ Steps

    /**
     * Get steps
     *
     * @return array
     */
    public function getSteps()
    {
        if (!isset($this->steps)) {
            $this->steps = $this->defineSteps();
            $this->processSteps();
        }

        return $this->steps;
    }

    /**
     * Get current step
     *
     * @param boolean $reset Reset flag OPTIONAL
     *
     * @return \XLite\Logic\ARepoStep
     */
    public function getStep($reset = false)
    {
        if (!isset($this->currentStep) || $reset) {
            $this->currentStep = $this->defineStep();
        }

        $steps = $this->getSteps();

        return isset($this->currentStep) && isset($steps[$this->currentStep]) ? $steps[$this->currentStep] : null;
    }

    /**
     * Define steps
     *
     * @return array
     */
    abstract protected function defineSteps();

    /**
     * Process steps
     *
     * @return void
     */
    protected function processSteps()
    {
        if ($this->getOptions()->include) {
            foreach ($this->steps as $i => $step) {
                if (!in_array($step, $this->getOptions()->include)) {
                    unset($this->steps[$i]);
                }
            }
        }

        foreach ($this->steps as $i => $step) {
            if (\XLite\Core\Operator::isClassExists($step)) {
                $this->steps[$i] = new $step($this);

            } else {
                unset($this->steps[$i]);
            }
        }

        $this->steps = array_values($this->steps);
    }

    /**
     * Define current step
     *
     * @return integer
     */
    protected function defineStep()
    {
        $currentStep = null;

        if (!Database::getRepo('XLite\Model\TmpVar')->getVar(static::getCancelFlagVarName())) {
            $i = $this->getOptions()->position;
            foreach ($this->getSteps() as $n => $step) {
                if ($i < $step->count()) {
                    $currentStep = $n;
                    $step->seek($i);
                    break;

                } else {
                    $i -= $step->count();
                }
            }
        }

        return $currentStep;
    }

    // }}}

    // {{{ SeekableIterator, Countable

    /**
     * \SeekableIterator::seek
     *
     * @param integer $position Position
     *
     * @return void
     */
    public function seek($position)
    {
        if ($position < $this->count()) {
            $this->getOptions()->position = $position;
            $this->getStep(true);
        }
    }

    /**
     * \SeekableIterator::current
     *
     * @return \XLite\Logic\ARepoStep
     */
    public function current()
    {
        return $this->getStep()->current();
    }

    /**
     * \SeekableIterator::key
     *
     * @return integer
     */
    public function key()
    {
        return $this->getOptions()->position;
    }

    /**
     * \SeekableIterator::next
     *
     * @return void
     */
    public function next()
    {
        $this->getOptions()->position++;
        $this->getStep()->next();
        if ($this->getStep()->key() >= $this->getStep()->count()) {
            $this->switchStep();
        }
    }

    protected function switchStep()
    {
        $this->getStep(true);
    }

    /**
     * \SeekableIterator::rewind
     *
     * @return void
     */
    public function rewind()
    {
    }

    /**
     * \SeekableIterator::valid
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->getStep() && $this->getStep()->valid() && !$this->hasErrors();
    }

    /**
     * \Countable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            $this->countCache = 0;
            foreach ($this->getSteps() as $step) {
                $this->countCache += $step->count();
            }
        }

        return $this->countCache;
    }

    // }}}

    // {{{ Error / warning routines

    /**
     * Add error
     *
     * @param string $title Title
     * @param string $body  Body
     *
     * @return void
     */
    public function addError($title, $body)
    {
        $this->getOptions()->errors[] = [
            'title' => $title,
            'body'  => $body,
        ];
    }

    /**
     * Get registered errors
     *
     * @return array
     */
    public function getErrors()
    {
        return empty($this->getOptions()->errors) ? [] : $this->getOptions()->errors;
    }

    /**
     * Check - has registered errors or not
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return !empty($this->getOptions()->errors);
    }

    // }}}

    // {{{ Service variable names

    /**
     * Get resizeTickDuration TmpVar name
     *
     * @return string
     */
    public static function getTickDurationVarName()
    {
        return static::getEventName() . 'TickDuration';
    }

    /**
     * Get resize cancel flag name
     *
     * @return string
     */
    public static function getCancelFlagVarName()
    {
        return static::getEventName() . 'CancelFlag';
    }

    // }}}

    /**
     * Get export lock key
     *
     * @return string
     */
    public static function getLockKey()
    {
        return static::getEventName();
    }

    /**
     * Lock with file lock
     */
    public static function lock()
    {
        FileLock::getInstance()->setRunning(
            static::getLockKey()
        );
    }

    /**
     * Check if is locked right now
     */
    public static function isLocked()
    {
        return FileLock::getInstance()->isRunning(
            static::getLockKey(),
            true
        );
    }

    /**
     * Unlock
     */
    public static function unlock()
    {
        FileLock::getInstance()->release(
            static::getLockKey()
        );
    }
}