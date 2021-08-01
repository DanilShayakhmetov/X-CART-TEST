<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Client;

interface XCartInterface
{
    /**
     * @param string $name
     * @param string $rebuildId
     * @param string $cacheId
     *
     * @return mixed|null
     */
    public function executeRebuildStep($name, $rebuildId, $cacheId);

    /**
     * @param string $file
     * @param array  $state
     * @param string $rebuildId
     * @param string $cacheId
     *
     * @return mixed|null
     */
    public function executeHook($file, $state, $rebuildId, $cacheId);
}
