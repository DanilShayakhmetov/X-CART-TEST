<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types\Output;

use GraphQL\Type\Definition\Type;
use Silex\Application;
use XCart\Bus\Query\Resolver\ModulesResolver;
use XCart\Bus\Query\Resolver\ScenarioResolver;
use XCart\Bus\Query\Types\AObjectType;
use XCart\Bus\Query\Types\StateDiffEnumType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ModuleTransitionType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'name'        => 'ModuleTransition',
            'description' => 'X-Cart rebuild module transition',
            'fields'      => [
                'id'                   => Type::id(),
                'stateAfterTransition' => $this->app[StateAfterTransitionType::class],
                'transition'           => $this->app[StateDiffEnumType::class],
                'info'                 => [
                    'type'    => $this->app[TransitionInfoType::class],
                    'resolve' => $this->app[ScenarioResolver::class . ':resolveScenarioInfo'],
                ],
            ],
        ];
    }
}
