<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Context;
use XCart\Bus\Query\Data\GDPRModulesDataSource;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class GDPRModulesResolver
{
    /**
     * @var GDPRModulesDataSource
     */
    private $GDPRModulesDataSource;

    /**
     * @var ModulesResolver
     */
    private $modulesResolver;

    /**
     * @param GDPRModulesDataSource $GDPRModulesDataSource
     * @param ModulesResolver       $modulesResolver
     */
    public function __construct(
        GDPRModulesDataSource $GDPRModulesDataSource,
        ModulesResolver $modulesResolver
    ) {
        $this->GDPRModulesDataSource = $GDPRModulesDataSource;
        $this->modulesResolver       = $modulesResolver;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param             $value
     * @param             $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return array
     *
     * @Resolver()
     */
    public function getList($value, $args, Context $context, ResolveInfo $info): array
    {
        return array_map(function ($moduleId) use ($context) {
            return $this->modulesResolver->getModule(Module::convertModuleId($moduleId), $context);
        }, $this->GDPRModulesDataSource->getAll());
    }
}
