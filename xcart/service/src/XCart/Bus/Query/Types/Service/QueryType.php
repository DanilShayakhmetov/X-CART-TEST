<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types\Service;

use GraphQL\Type\Definition\Type;
use XCart\Bus\Query\Resolver\ModulesResolver;
use XCart\Bus\Query\Resolver\ServiceRebuildResolver;
use XCart\Bus\Query\Resolver\UpgradeResolver;
use XCart\Bus\Query\Types\AObjectType;
use XCart\Bus\Query\Types\Output\AvailableUpgradeType;
use XCart\Bus\Query\Types\Output\ModulesPageType;
use XCart\Bus\Query\Types\Output\ModuleType;
use XCart\Bus\Query\Types\Output\RebuildStateType;
use XCart\Bus\Query\Types\Service\Output\ScriptStepStateType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class QueryType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'name'   => 'Query',
            'fields' => [
                'modules' => [
                    'description' => 'Returns list of modules',
                    'type'        => $this->app[ModulesPageType::class],
                    'resolve'     => $this->app[ModulesResolver::class . ':resolvePage'],
                    'args'        => [
                        'language'       => Type::string(),
                        'version'        => Type::string(),
                        'search'         => Type::string(),
                        'installed'      => Type::boolean(),
                        'custom'         => Type::boolean(),
                        'private'        => Type::boolean(),
                        'canInstall'     => Type::boolean(),
                        'system'         => Type::boolean(),
                        'type'           => Type::string(),
                        'enabled'        => Type::string(),
                        'payable'        => Type::string(),
                        'tag'            => Type::string(),
                        'nonFreeEdition' => Type::boolean(),
                        'licensed'       => Type::boolean(),
                        'sort'           => Type::listOf(Type::string()),
                        'limit'          => Type::listOf(Type::int()),
                        'scenario'       => Type::id(),
                        'isSalesChannel' => Type::boolean(),
                        'isLanding'      => Type::boolean(),
                        'includeIds'     => Type::listOf(Type::id()),
                        'integrityCheck' => Type::boolean(),
                        'existsUpdate'   => Type::string(),
                    ],
                ],

                'module' => [
                    'type'    => $this->app[ModuleType::class],
                    'resolve' => $this->app[ModulesResolver::class . ':resolveModule'],
                    'args'    => [
                        'id'       => Type::id(),
                        'language' => Type::string(),
                    ],
                ],

                'availableUpgradeTypes' => [
                    'description' => 'Returns list of available upgrade types',
                    'type'        => Type::listOf($this->app[AvailableUpgradeType::class]),
                    'resolve'     => $this->app[UpgradeResolver::class . ':getAvailableUpgradeTypes'],
                ],

                'rebuildState' => [
                    'description' => 'Returns current rebuild state',
                    'type'        => $this->app[RebuildStateType::class],
                    'resolve'     => $this->app[ServiceRebuildResolver::class . ':getCurrentStateInfo'],
                    'args'        => [
                        'id' => [
                            'description' => 'Rebuild state id',
                            'type'        => Type::id(),
                        ],
                    ],
                ],

                'rebuildStepState' => [
                    'description' => 'Returns current state of rebuild step',
                    'type'        => $this->app[ScriptStepStateType::class],
                    'resolve'     => $this->app[ServiceRebuildResolver::class . ':getStepStateInfo'],
                    'args'        => [
                        'id' => [
                            'description' => 'Rebuild state id',
                            'type'        => Type::id(),
                        ],
                    ],
                ],
            ],
        ];
    }
}
