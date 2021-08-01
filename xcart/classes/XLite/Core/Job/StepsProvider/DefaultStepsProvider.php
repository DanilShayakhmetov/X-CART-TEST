<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job\StepsProvider;

use XLite\Core\Job\Job;

/**
 * Class DefaultStepsProvider
 */
class DefaultStepsProvider implements StepsProviderInterface
{
    protected $count;

    /**
     * @var \SeekableIterator
     */
    private $steps;

    function __construct(\SeekableIterator $steps, $count = null)
    {
        $this->steps = $steps;
        $this->count = $count ?: iterator_count($steps);
        $steps->rewind();
    }

    /**
     * @param $position
     * @param $size
     *
     * @return Job[]
     */
    public function getBatch($position, $size)
    {
        $result = [];

        $this->steps->seek($position);

        for ($i = 0; $i < $size; $i++) {
            $result[]= $this->steps->current();
            $this->steps->next();

            if (!$this->steps->valid()) {
                break;
            }

        }

        return $result;
    }

    public function isValid()
    {
        return $this->steps->valid();
    }

    public function getCount()
    {
        return $this->count;
    }
}
