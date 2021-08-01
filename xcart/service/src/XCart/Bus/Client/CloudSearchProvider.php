<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Client;

use XCart\Bus\Domain\Module;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class CloudSearchProvider
{
    /**
     * @var MarketplaceClient
     */
    private $client;

    /**
     * @var array
     */
    private $runtimeCache = [];

    /**
     * @param MarketplaceClient $client
     */
    public function __construct(MarketplaceClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param $substring
     *
     * @return array
     */
    public function search($substring)
    {
        if (!isset($this->runtimeCache[$substring])) {
            // TODO: Provide 'wave' parameter and license keys
            $result = $this->client->searchModules($substring);

            if (isset($result['products'])) {
                $this->runtimeCache[$substring] = array_map(function ($item) {
                    return Module::convertModuleId($item['id']);
                }, $result['products']);
            } else {
                $this->runtimeCache[$substring] = [];
            }
        }

        return $this->runtimeCache[$substring];
    }
}
