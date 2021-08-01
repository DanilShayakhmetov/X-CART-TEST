<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job\StepsProvider;

use XLite\Core\Job\Job;

/**
 * Class GeneratingStepsProvider
 */
class GeneratingStepsProvider implements StepsProviderInterface
{
    protected $generator;

    protected $count;

    function __construct(JobGeneratorInterface $generator, $count)
    {
        $this->generator = $generator;
        $this->count = $count;
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

        for ($i = 0; $i < $size; $i++) {
            if (!$this->isValid()) {
                break;
            }

            $result[] = $this->generator->getNextJob();
        }

        return $result;
    }

    public function isValid()
    {
        return $this->generator->isValid();
    }

    public function getCount()
    {
        return $this->count;
    }
}
