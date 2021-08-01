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
class ModuleIntegrityViolationsType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'name'        => 'ModuleIntegrityViolationsType',
            'description' => 'Module\'s integrity violations structure',
            'fields'      => [
                'entries'  => Type::listOf($this->app[ModuleIntegrityViolationEntryType::class]),
                'isFinal'  => Type::boolean(),
                'progress' => Type::float(),
                'error'    => Type::string()
            ],
        ];
    }
}
