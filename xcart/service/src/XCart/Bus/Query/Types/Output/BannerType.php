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
use XCart\Bus\Query\Types\Scalar\WordType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class BannerType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'fields' => [
                'image'   => [
                    'type'        => Type::nonNull($this->app[UrlType::class]),
                    'description' => 'Image URL',
                ],
                'module'  => [
                    'type'        => $this->app[ModuleNameType::class],
                    'description' => 'Linked module name',
                ],
                'url'     => [
                    'type'        => $this->app[UrlType::class],
                    'description' => 'Href URL',
                ],
                'section' => [
                    'type'        => $this->app[WordType::class],
                    'description' => 'Section',
                ],
            ],
        ];
    }
}
