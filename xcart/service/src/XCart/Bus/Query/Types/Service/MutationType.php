<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types\Service;

use GraphQL\Type\Definition\Type;
use XCart\Bus\Query\Resolver\LicenseResolver;
use XCart\Bus\Query\Resolver\ServiceChangeDomainResolver;
use XCart\Bus\Query\Resolver\ServiceRebuildResolver;
use XCart\Bus\Query\Types\AObjectType;
use XCart\Bus\Query\Types\Output\ChangedConfigValueType;
use XCart\Bus\Query\Types\Output\LicenseStateType;
use XCart\Bus\Query\Types\Output\RebuildStateType;
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
                'registerLicenseKey' => [
                    'description' => 'Register license',
                    'type'        => $this->app[LicenseStateType::class],
                    'resolve'     => $this->app[LicenseResolver::class . ':register'],
                    'args'        => [
                        'key'   => Type::string(),
                        'email' => Type::string(),
                    ],
                ],

                'clearCache' => [
                    'type'    => Type::boolean(),
                    'resolve' => $this->app[LicenseResolver::class . ':clearCache'],
                ],

                'rebuild' => [
                    'description' => 'Prepares the rebuild execution, checks the environment and returns the initial rebuild state',
                    'type'        => $this->app[RebuildStateType::class],
                    'resolve'     => $this->app[ServiceRebuildResolver::class . ':rebuild'],
                ],

                'executeRebuild' => [
                    'description' => 'Runs the rebuild script execution step, returning the execution state',
                    'type'        => $this->app[RebuildStateType::class],
                    'resolve'     => $this->app[ServiceRebuildResolver::class . ':executeRebuild'],
                    'args'        => [
                        'id'              => [
                            'description' => 'Rebuild state id',
                            'type'        => Type::id(),
                        ],
                        'action'          => [
                            'description' => 'Execution action: execute (default), retry, ignore, release [the hold]',
                            'type'        => Type::string(),
                        ],
                        'resetConnection' => [
                            'description' => 'Reset the connection without waiting for a response',
                            'type'        => Type::boolean(),
                        ],
                    ],
                ],

                'dropRebuild' => [
                    'description' => 'Remove all active rebuild process',
                    'type'        => Type::boolean(),
                    'resolve'     => $this->app[ServiceRebuildResolver::class . ':dropRebuild'],
                ],

                'upgrade' => [
                    'description' => 'Upgrade modules, core, service tool',
                    'type'        => $this->app[RebuildStateType::class],
                    'resolve'     => $this->app[ServiceRebuildResolver::class . ':upgrade'],
                    'args'        => [
                        'type'    => [
                            'description' => 'Upgrade Type (Possible values: build, minor, major, core, self)',
                            'type'        => Type::string(),
                        ],
                        'modules' => [
                            'description' => 'Modules list to upgrade',
                            'type'        => Type::listOf(Type::id()),
                        ],
                    ],
                ],

                'setModulesState' => [
                    'description' => 'Enable, disable, install and remove modules from the list',
                    'type'        => $this->app[RebuildStateType::class],
                    'resolve'     => $this->app[ServiceRebuildResolver::class . ':setModulesState'],
                    'args'        => [
                        'enable'  => [
                            'description' => 'Modules list to enable',
                            'type'        => Type::listOf(Type::id()),
                        ],
                        'disable' => [
                            'description' => 'Modules list to disable',
                            'type'        => Type::listOf(Type::id()),
                        ],
                        'install' => [
                            'description' => 'Modules list to install',
                            'type'        => Type::listOf(Type::id()),
                        ],
                        'remove'  => [
                            'description' => 'Modules list to remove',
                            'type'        => Type::listOf(Type::id()),
                        ],
                    ],
                ],

                'setDomainName' => [
                    'description' => 'Set domain for customer area',
                    'type'        => $this->app[ChangedConfigValueType::class],
                    'resolve'     => $this->app[ServiceChangeDomainResolver::class . ':setNewDomainName'],
                    'args'        => [
                        'domain' => [
                            'description' => 'New domain name',
                            'type'        => Type::string(),
                        ],
                    ],
                ],

            ],
        ];
    }
}
