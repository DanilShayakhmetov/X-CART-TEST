<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job\StepsProvider;

/**
 * Interface StepsProvider
 * @package XLite\Core\Job
 */
interface StepsProviderInterface
{
    /**
     * @param $position
     * @param $size
     *
     * @return mixed
     */
    public function getBatch($position, $size);

    /**
     * @return mixed
     */
    public function isValid();

    /**
     * @return mixed
     */
    public function getCount();
}
