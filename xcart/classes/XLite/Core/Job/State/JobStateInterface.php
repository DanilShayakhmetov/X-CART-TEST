<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job\State;


/**
 * Interface JobStateInterface
 *
 * =========== N.B. ===========
 * In current implementation this is used in both executingSide and schedulingSide,
 * but there is possibility that executingSide will be implemented differently
 * so executing side should follow this interface, but can't use it
 */
interface JobStateInterface
{
    /**
     * @return int
     */
    public function getProgress();
    public function setProgress($progress);

    /**
     * @return boolean
     */
    public function isStarted();
    public function getStartedAt();
    public function setStartedAt($value);

    /**
     * @return boolean
     */
    public function isFinished();
    public function setFinished($value);

    /**
     * @return boolean
     */
    public function isCancelled();
    public function setCancelled($value);

    /**
     * @return array
     */
    public function getData($name);
    public function setData($name, $value);

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return mixed
     */
    public function toArrayForNative();
}
