<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types;

use GraphQL\Type\Definition\Type;
use XCart\Bus\Query\Resolver\LicenseResolver;
use XCart\Bus\Query\Resolver\RebuildResolver;
use XCart\Bus\Query\Resolver\ScenarioResolver;
use XCart\Bus\Query\Resolver\UpgradeResolver;
use XCart\Bus\Query\Resolver\WavesResolver;
use XCart\Bus\Query\Types\Input\ChangeModuleStateType;
use XCart\Bus\Query\Types\Input\RebuildActionParamsType;
use XCart\Bus\Query\Types\Output\AlertType;
use XCart\Bus\Query\Types\Output\LicenseStateType;
use XCart\Bus\Query\Types\Output\RebuildStateType;
use XCart\Bus\Query\Types\Output\ScenarioType;
use XCart\Bus\Query\Types\Output\WaveType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class MutationType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'name'   => 'Mutation',
            'fields' => [
                'createScenario'          => [
                    'type'    => $this->app[ScenarioType::class],
                    'args'    => [
                        'type'      => Type::string(),
                        'returnUrl' => Type::string(),
                    ],
                    'resolve' => $this->app[ScenarioResolver::class . ':createScenario'],
                ],
                'fillScenario'            => [
                    'type'    => $this->app[ScenarioType::class],
                    'args'    => [
                        'scenarioId' => Type::nonNull(Type::id()),
                        'type'       => Type::nonNull(Type::string()),
                        'ids'        => Type::listOf(Type::id()),
                    ],
                    'resolve' => $this->app[UpgradeResolver::class . ':fillScenario'], // todo: use scenario resolver
                ],
                'discardScenario'         => [
                    'args'    => [
                        'scenarioId' => Type::nonNull(Type::id()),
                    ],
                    'type'    => $this->app[ScenarioType::class],
                    'resolve' => $this->app[ScenarioResolver::class . ':discardScenario'],
                ],
                'changeModulesState'      => [
                    'args'    => [
                        'states'     => Type::nonNull(Type::listOf($this->app[ChangeModuleStateType::class])),
                        'scenarioId' => Type::nonNull(Type::id()),
                        'language'   => [
                            'type'        => Type::string(),
                            'description' => 'Locale to output',
                        ],
                    ],
                    'type'    => $this->app[ScenarioType::class],
                    'resolve' => $this->app[ScenarioResolver::class . ':changeModulesState'],
                ],
                'changeSkinState'         => [
                    'args'    => [
                        'moduleId'  => Type::nonNull(Type::id()),
                        'returnUrl' => Type::string(),
                    ],
                    'type'    => $this->app[ScenarioType::class],
                    'resolve' => $this->app[ScenarioResolver::class . ':changeSkinState'],
                ],
                'startRebuild'            => [
                    'type'        => $this->app[RebuildStateType::class],
                    'description' => 'Prepares the rebuild execution, checks the environment and returns the initial rebuild state',
                    'args'        => [
                        'id'               => [
                            'type'        => Type::id(),
                            'description' => 'Scenario id',
                        ],
                        'type'             => [
                            'type' => Type::string(),
                        ],
                        'reason'           => [
                            'type' => Type::string(),
                        ],
                        'failureReturnUrl' => [
                            'type'        => Type::string(),
                            'description' => 'Failure return URL',
                        ],
                    ],
                    'resolve'     => $this->app[RebuildResolver::class . ':startRebuild'],
                ],
                'startRollback'           => [
                    'type'        => $this->app[RebuildStateType::class],
                    'description' => 'Prepares the rollback execution',
                    'args'        => [
                        'id' => [
                            'type'        => Type::id(),
                            'description' => 'Rebuild state ID',
                        ],
                    ],
                    'resolve'     => $this->app[RebuildResolver::class . ':startRollback'],
                ],
                'dropRebuild'             => [
                    'type'    => Type::boolean(),
                    'resolve' => $this->app[RebuildResolver::class . ':dropRebuild'],
                ],
                'clearCache'             => [
                    'type'    => Type::boolean(),
                    'resolve' => $this->app[LicenseResolver::class . ':clearCache'],
                ],
                'executeRebuild'          => [
                    'type'        => $this->app[RebuildStateType::class],
                    'description' => 'Runs the rebuild script execution step, returning the execution state',
                    'args'        => [
                        'id'     => [
                            'type'        => Type::id(),
                            'description' => 'Rebuild state id',
                        ],
                        'action' => [
                            'type'        => Type::string(), // todo: enum [execute, retry, ignore, release]
                            'description' => 'execution action: execute (default), retry, ignore, release [the hold]',
                        ],
                        'params' => [
                            'type'        => $this->app[RebuildActionParamsType::class],
                            'description' => 'Execution action optional params',
                        ],
                    ],
                    'resolve'     => $this->app[RebuildResolver::class . ':executeRebuild'],
                ],
                'registerLicenseKey'      => [
                    'type'    => $this->app[LicenseStateType::class],
                    'args'    => [
                        'key'   => Type::string(),
                        'email' => Type::string(),
                    ],
                    'resolve' => $this->app[LicenseResolver::class . ':register'],
                ],
                'setWave'                 => [
                    'type'    => $this->app[WaveType::class],
                    'args'    => [
                        'wave' => Type::string(),
                    ],
                    'resolve' => $this->app[WavesResolver::class . ':changeWave'],
                ],
                'requestForUpgrade'       => [
                    'type'    => Type::listOf($this->app[AlertType::class]),
                    'args'    => [
                        'id' => Type::id(),
                    ],
                    'resolve' => $this->app[UpgradeResolver::class . ':requestForUpgrade'],
                ],
                'removeUnallowedModules'  => [
                    'type'    => $this->app[ScenarioType::class],
                    'args'    => [
                        'returnUrl' => Type::string(),
                    ],
                    'resolve' => $this->app[ScenarioResolver::class . ':mutateRemoveUnallowedModules'],
                ],
                'disableUnallowedModules' => [
                    'type'    => $this->app[ScenarioType::class],
                    'args'    => [
                        'returnUrl' => Type::string(),
                    ],
                    'resolve' => $this->app[ScenarioResolver::class . ':mutateDisableUnallowedModules'],
                ],
            ],
        ];
    }
}
