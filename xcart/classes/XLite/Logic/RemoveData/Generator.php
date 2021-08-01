<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\RemoveData;


/**
 * Generator
 */
class Generator extends \XLite\Logic\AGenerator
{
    /**
     * Flag: is process in progress (true) or no (false)
     *
     * @var boolean
     */
    protected static $inProgress = false;

    /**
     * Set inProgress flag value
     *
     * @param boolean $value Value
     *
     * @return void
     */
    public function setInProgress($value)
    {
        static::$inProgress = $value;
    }

    // {{{ Steps

    /**
     * @return array
     */
    protected function getStepsList()
    {
        return [
            'XLite\Logic\RemoveData\Step\Products',
            'XLite\Logic\RemoveData\Step\Categories',
            'XLite\Logic\RemoveData\Step\Orders',
            'XLite\Logic\RemoveData\Step\Customers',
            'XLite\Logic\RemoveData\Step\ClassesAndAttributes',
        ];
    }

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        $steps = [];
        $options = $this->getOptions();
        if (isset($options['steps'])) {
            $requestedSteps = $options['steps'];

            if (is_array($requestedSteps)) {
                foreach ($this->getStepsList() as $step) {
                    $_step = explode('\\', $step);
                    $_step = array_pop($_step);

                    if (in_array($_step, $requestedSteps)) {
                        $steps[] = $step;
                    }
                }
            }
        }

        return $steps;
    }

    // }}}

    // {{{ SeekableIterator, Countable

    /**
     * @inheritdoc
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            if (!isset($this->options['count'])) {
                $this->options['count'] = 0;
                foreach ($this->getSteps() as $step) {
                    $this->options['count'] += $step->count();
                    $this->options['count' . get_class($step)] = $step->count();
                }
            }
            $this->countCache = $this->options['count'];
        }

        return $this->countCache;
    }

    // }}}

    // {{{ Service variable names

    /**
     * Get event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'removeData';
    }

    // }}}
}
