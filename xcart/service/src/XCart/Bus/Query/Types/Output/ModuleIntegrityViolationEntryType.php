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
class ModuleIntegrityViolationEntryType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'name'        => 'ModuleIntegrityViolationType',
            'description' => 'Module\'s integrity violation entry',
            'fields'      => [
                'filepath'    => Type::string(),
                'hash_actual' => Type::string(),
                'hash_known'  => Type::string(),
                'type'        => Type::string(),
            ],
        ];
    }
}
