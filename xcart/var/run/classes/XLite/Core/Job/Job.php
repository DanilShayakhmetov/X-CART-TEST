<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job;

/**
 * Interface Job
 * @package XLite\Core\Job
 */
interface Job
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return void
     */
    public function handle();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPreferredQueue();
}
