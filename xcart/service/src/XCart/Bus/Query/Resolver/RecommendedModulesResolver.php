<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use XCart\Bus\Query\Context;
use XCart\Bus\Query\Data\RecommendedModulesDataSource;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class RecommendedModulesResolver
{
    /**
     * @var RecommendedModulesDataSource
     */
    private $recommendedModulesDataSource;

    /**
     * RecommendedModulesResolver constructor.
     *
     * @param RecommendedModulesDataSource $recommendedModulesDataSource
     */
    public function __construct(RecommendedModulesDataSource $recommendedModulesDataSource)
    {
        $this->recommendedModulesDataSource = $recommendedModulesDataSource;
    }

    /**
     * @param mixed       $value
     * @param array       $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return Deferred
     *
     * @Resolver()
     */
    public function getList($value, $args, $context, ResolveInfo $info)
    {
        $this->recommendedModulesDataSource->loadDeferred();

        return new Deferred(function () use ($args) {
            $modules = $this->recommendedModulesDataSource->findByPageParams($args['pageParams']);

            return array_slice($modules, 0, $args['limit'] ?? null);
        });
    }
}