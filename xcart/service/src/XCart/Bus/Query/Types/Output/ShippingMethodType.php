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
class ShippingMethodType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'fields' => [
                'processor'        => [
                    'type'        => Type::string(),
                    'description' => 'Processor service name',
                ],
                'carrier'          => [
                    'type'        => Type::string(),
                    'description' => 'Carrier service name',
                ],
                'code'             => [
                    'type'        => Type::string(),
                    'description' => 'Code',
                ],
                'enabled'          => [
                    'type'        => Type::boolean(),
                    'description' => 'Stub',
                ],
                'added'            => [
                    'type'        => Type::boolean(),
                    'description' => 'Stub',
                ],
                'moduleName'      => [
                    'type'        => $this->app[ModuleNameType::class],
                    'description' => 'Module name',
                ],
                'translations'     => [
                    'type'        => Type::listOf($this->app[ShippingTranslationType::class]),
                    'description' => 'Method name translations',
                ],
                'fromMarketplace' => [
                    'type'        => Type::boolean(),
                    'description' => 'Flag',
                ],
                'iconURL'         => [
                    'type'        => $this->app[UrlType::class],
                    'description' => 'Method icon URL',
                ],
            ],
        ];
    }
}
