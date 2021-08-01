<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use XCart\Bus\Query\Types\ObjectTypeTrait;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class RebuildActionParamsType extends InputObjectType
{
    use ObjectTypeTrait;

    const REPLACE_ALL   = 'all';
    const KEEP_SELECTED = 'selected';

    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'name'   => 'RebuildActionParams',
            'fields' => [
                'replaceModified'     => [
                    'type'        => Type::string(),
                    'description' => 'Choice about keeping or replacing the modified files. Accepts "all" or "selected" value.',
                ],
                'filesToKeep'         => [
                    'type'        => Type::listOf(Type::string()),
                    'description' => 'List of file paths to keep as modified during upgrade.',
                ],
                'executeModulesHooks' => [
                    'type'        => Type::listOf(Type::string()),
                    'description' => 'List of modules to execute posponed pre_upgrade and post_upgrade hooks',
                ],
            ],
        ];
    }
}
