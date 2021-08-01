<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types\Output;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Silex\Application;
use XCart\Bus\Query\Context;
use XCart\Bus\Query\Resolver\ModulesResolver;
use XCart\Bus\Query\Types\AObjectType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class InstallationData extends AObjectType
{
    /**
     * @var ModulesResolver
     */
    private $modulesResolver;

    /**
     * @param Application     $app
     * @param ModulesResolver $modulesResolver
     */
    public function __construct(
        Application $app,
        ModulesResolver $modulesResolver
    ) {
        parent::__construct($app);

        $this->modulesResolver = $modulesResolver;
    }

    /**
     * @return array
     */
    protected function defineConfig(): array
    {
        return [
            'fields' => [
                'installationDate'        => Type::int(),
                'trialExpired'            => Type::boolean(),
                'backupMasterIsEnabled'   => Type::boolean(),
                'backupMasterIsInstalled' => Type::boolean(),
                'purchasesCount'          => [
                    'type'    => Type::int(),
                    'resolve' => function ($value, $args, Context $context, ResolveInfo $info) {
                        $purchased = $this->modulesResolver->resolvePage([], ['licensed' => true], $context, $info);

                        return $purchased['count'];
                    },
                ],
                'coreVersion'             => Type::string(),
            ],
        ];
    }
}
