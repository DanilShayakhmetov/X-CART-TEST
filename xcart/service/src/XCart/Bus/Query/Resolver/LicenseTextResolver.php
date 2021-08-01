<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use XCart\Bus\Client\MarketplaceClient;
use XCart\Bus\Query\Data\MarketplaceModulesDataSource;
use XCart\SilexAnnotations\Annotations\Service;
use XCart\Bus\Core\Annotations\Resolver;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * @Service\Service()
 */
class LicenseTextResolver
{
    /**
     * @var MarketplaceClient
     */
    protected $marketplaceClient;

    /**
     * @var MarketplaceModulesDataSource
     */
    protected $marketplaceModulesDataSource;

    /**
     * LicenseTextResolver constructor.
     *
     * @param MarketplaceClient $marketplaceClient
     * @param MarketplaceModulesDataSource $marketplaceModulesDataSource
     */
    public function __construct(
        MarketplaceClient $marketplaceClient,
        MarketplaceModulesDataSource $marketplaceModulesDataSource
    )
    {
        $this->marketplaceClient = $marketplaceClient;
        $this->marketplaceModulesDataSource = $marketplaceModulesDataSource;
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return array
     *
     * @Resolver()
     */
    public function getLicensesText($value, $args, $context, ResolveInfo $info): array
    {
        $result = [];
        $modulesId = $args['modulesId'] ?? [];

        foreach ($modulesId as $moduleId) {
            $moduleInfo = $this->getModuleInfo($moduleId);
            if ($moduleInfo) {
                $result[] = [
                    'moduleId'    => $moduleId,
                    'moduleName'  => $moduleInfo['readableName'],
                    'hasLicense'  => $moduleInfo['has_license'],
                    'licenseText' => $moduleInfo['license'],
                ];
            }
        }

        return $result;
    }

    public function getModuleInfo($moduleId): array
    {
        $module = $this->marketplaceModulesDataSource->find($moduleId);

        return $module ? $this->marketplaceClient->getModuleInfo($module[0]) : [];
    }

}