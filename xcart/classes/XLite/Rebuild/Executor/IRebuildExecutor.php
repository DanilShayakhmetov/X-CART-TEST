<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Rebuild\Executor;


interface IRebuildExecutor
{
    /**
     * @param mixed $payloadData
     *
     * @return boolean
     */
    public function isApplicable($payloadData);

    /**
     * @param mixed $payloadData
     * @param string $rebuildId
     * @return array
     */
    public function execute($payloadData, $rebuildId);
}