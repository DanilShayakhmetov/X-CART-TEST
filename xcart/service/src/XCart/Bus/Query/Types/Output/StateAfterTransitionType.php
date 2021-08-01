<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types\Output;

use GraphQL\Type\Definition\Type;
use XCart\Bus\Query\Types\AObjectType;
use XCart\Bus\Query\Types\Scalar\VersionType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class StateAfterTransitionType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'name'        => 'StateAfterTransition',
            'description' => 'X-Cart state after transition',
            'fields'      => [
                'enabled'   => Type::boolean(),
                'installed' => Type::boolean(),
                'upgraded'  => Type::boolean(),
                'version'   => $this->app[VersionType::class],
            ],
        ];
    }
}
