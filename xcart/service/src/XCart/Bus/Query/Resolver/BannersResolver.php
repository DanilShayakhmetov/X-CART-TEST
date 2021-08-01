<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\Bus\Query\Data\BannersDataSource;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class BannersResolver
{
    /**
     * @var BannersDataSource
     */
    private $bannersDataSource;

    /**
     * @param BannersDataSource $bannersDataSource
     */
    public function __construct(BannersDataSource $bannersDataSource)
    {
        $this->bannersDataSource = $bannersDataSource;
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
    public function getList($value, $args, $context, ResolveInfo $info)
    {
        $this->bannersDataSource->loadDeferred();

        return new Deferred(function () {
            return $this->bannersDataSource->getAll();
        });
    }
}
