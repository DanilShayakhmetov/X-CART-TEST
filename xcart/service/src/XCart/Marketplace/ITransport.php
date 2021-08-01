<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace;

interface ITransport
{
    const TTL_DEFAULT = 30;
    const TTL_LONG    = 60;

    /**
     * @param array $config
     */
    public function __construct(array $config = []);

    /**
     * @param string $path
     *
     * @return array
     */
    public function getFileContent($path);

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param string $action
     * @param array  $data
     * @param array  $headers
     * @param int    $ttl
     *
     * @return array
     */
    public function doAPIRequest($action, array $data = [], array $headers = [], $ttl = self::TTL_DEFAULT);
}
