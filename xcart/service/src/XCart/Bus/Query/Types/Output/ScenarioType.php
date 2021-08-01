<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types\Output;

use GraphQL\Type\Definition\Type;
use XCart\Bus\Query\Types\AObjectType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ScenarioType extends AObjectType
{
    protected function defineConfig()
    {
        return [
            'name'        => 'Scenario',
            'description' => 'X-Cart rebuild scenario',
            'fields'      => function () {
                return [
                    'id'                 => Type::id(),
                    'date'               => Type::int(),
                    'updatedAt'          => Type::int(),
                    'type'               => Type::string(),
                    'modulesTransitions' => Type::listOf($this->app[ModuleTransitionType::class]),
                ];
            },
        ];
    }
}
