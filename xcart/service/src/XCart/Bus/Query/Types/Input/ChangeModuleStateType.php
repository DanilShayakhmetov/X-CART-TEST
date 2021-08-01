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
use XCart\Bus\Query\Types\Scalar\VersionType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ChangeModuleStateType extends InputObjectType
{
    use ObjectTypeTrait;

    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'name'   => 'ChangeModuleStateInput',
            'fields' => [
                'id'      => [
                    'type'        => Type::id(),
                    'description' => 'Module id',
                ],
                'enable'  => [
                    'type'        => Type::boolean(),
                    'description' => 'Marked to be enabled or disabled',
                ],
                'install' => [
                    'type'        => Type::boolean(),
                    'description' => 'Marked to be installed',
                ],
                'remove'  => [
                    'type'        => Type::boolean(),
                    'description' => 'Marked to be removed',
                ],
                'upgrade' => [
                    'type'        => Type::boolean(),
                    'description' => 'Marked to be upgraded',
                ],
                'version' => [
                    'type'        => $this->app[VersionType::class],
                    'description' => 'Version to be set',
                ],
                'installLatestVersion' => [
                    'type'        => Type::boolean(),
                    'description' => 'Install latest version flag',
                ],
            ],
        ];
    }
}
