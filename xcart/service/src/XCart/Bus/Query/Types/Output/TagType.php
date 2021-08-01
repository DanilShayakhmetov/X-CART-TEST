<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types\Output;

use GraphQL\Type\Definition\Type;
use XCart\Bus\Query\Types\AObjectType;
use XCart\Bus\Query\Types\Scalar\ModuleNameType;
use XCart\Bus\Query\Types\Scalar\UrlType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class TagType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'fields' => [
                'id'       => Type::string(),
                'image'    => [
                    'type'        => $this->app[UrlType::class],
                    'description' => 'Tag Banner image URL',
                ],
                'module'   => [
                    'type'        => $this->app[ModuleNameType::class],
                    'description' => 'Tag banner linked module name',
                ],
                'url'      => [
                    'type'        => $this->app[UrlType::class],
                    'description' => 'Tag banner URL',
                ],
                'expires'  => [
                    'type'        => Type::string(),
                    'description' => 'Tag banner expiration date',
                ],
                'name'     => [
                    'type'        => Type::string(),
                    'description' => 'Tag name',
                ],
                'category' => [
                    'type'        => Type::string(),
                    'description' => 'Tag category',
                ],
                'showBanner' => Type::boolean(),
            ],
        ];
    }
}
