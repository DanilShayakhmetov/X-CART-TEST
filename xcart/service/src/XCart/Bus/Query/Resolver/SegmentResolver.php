<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use XCart\Bus\Client\MarketplaceClient;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\Bus\Query\Data\SegmentDataSource;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class SegmentResolver
{
    /**
     * @var SegmentDataSource
     */
    private $segmentDataSource;

    /**
     * @var MarketplaceClient
     */
    private $marketplaceClient;

    /**
     * @param SegmentDataSource $segmentDataSource
     * @param MarketplaceClient $marketplaceClient
     */
    public function __construct(
        SegmentDataSource $segmentDataSource,
        MarketplaceClient $marketplaceClient
    ) {
        $this->segmentDataSource = $segmentDataSource;
        $this->marketplaceClient = $marketplaceClient;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return Deferred
     *
     * @Resolver()
     */
    public function getData($value, $args, $context, ResolveInfo $info)
    {
        $segmentData = $this->segmentDataSource->getAll();
        $segmentData['user_hash'] = $this->marketplaceClient->getIntercomHash($segmentData['user_id'] ?? '');

        return $segmentData;
    }
}
