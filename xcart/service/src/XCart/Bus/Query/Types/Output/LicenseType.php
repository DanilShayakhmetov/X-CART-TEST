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
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Context;
use XCart\Bus\Query\Resolver\ModulesResolver;
use XCart\Bus\Query\Types\AObjectType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class LicenseType extends AObjectType
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
                'keyValue'         => Type::string(),
                'id'               => [
                    'type'    => Type::string(),
                    'resolve' => function ($value, $args, Context $context, ResolveInfo $info) {
                        return Module::buildModuleId($value['author'], $value['name']);
                    },
                ],
                'moduleName'       => [
                    'type'    => Type::string(),
                    'resolve' => function ($value, $args, Context $context, ResolveInfo $info) {
                        if ($value['author'] === 'CDev' && $value['name'] === 'Core') {
                            return 'X-Cart ' . $value['keyData']['editionName'] . ' license';
                        }

                        $module = $this->modulesResolver->getModule(
                            Module::buildModuleId($value['author'], $value['name']),
                            $context,
                            $args['language'] ?? null
                        );

                        return $module->moduleName ?? $value['name'];
                    },
                ],
                'expiration'       => [
                    'type'    => Type::int(),
                    'resolve' => function ($value, $args, Context $context, ResolveInfo $info) {
                        return (int) ($value['keyData']['expDate'] ?? 0);
                    },
                ],
                'xcnPlan'          => Type::string(),
                'keyData'          => $this->app[LicenseKeyDataType::class],
                'hasActualVersion' => Type::boolean(),
                'isInstalled'      => Type::boolean(),
            ],
        ];
    }
}
