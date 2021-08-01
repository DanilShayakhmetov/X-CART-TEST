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
class ScenarioResultEntryType extends AObjectType
{
    protected function defineConfig()
    {
        return [
            'name'        => 'ScenarioResultEntry',
            'description' => 'X-Cart rebuild scenario result entry',
            'fields'      => function () {
                return [
                    'transition' => Type::string(),
                    'module'     => $this->app[ModuleType::class],
                ];
            },
        ];
    }
}
