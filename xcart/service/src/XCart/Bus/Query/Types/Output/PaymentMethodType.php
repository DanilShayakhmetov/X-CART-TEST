<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types\Output;

use GraphQL\Type\Definition\Type;
use XCart\Bus\Query\Types\AObjectType;
use XCart\Bus\Query\Types\Scalar\FqcnType;
use XCart\Bus\Query\Types\Scalar\ModuleNameType;
use XCart\Bus\Query\Types\Scalar\UppercaseLetterType;
use XCart\Bus\Query\Types\Scalar\UrlType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class PaymentMethodType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'fields' => [
                'service_name'     => [
                    'type'        => Type::string(),
                    'description' => 'Service name',
                ],
                'class'            => [
                    'type'        => $this->app[FqcnType::class],
                    'description' => 'Processor class name',
                ],
                'type'             => [
                    'type'        => $this->app[UppercaseLetterType::class],
                    'description' => 'Payment type',
                ],
                'orderby'         => [
                    'type'        => Type::int(),
                    'description' => 'Method order for customer area',
                ],
                'adminOrderby'   => [
                    'type'        => Type::int(),
                    'description' => 'Method order for admin area',
                ],
                'countries'        => [
                    'type'        => Type::listOf(Type::string()), // @todo: country code type
                    'description' => 'Countries list',
                ],
                'exCountries'        => [
                    'type'        => Type::listOf(Type::string()),
                    'description' => 'Excluded countries list',
                ],
                'translations'     => [
                    'type'        => Type::listOf($this->app[PaymentTranslationType::class]),
                    'description' => 'Method name translations',
                ],
                'added'            => [
                    'type'        => Type::boolean(),
                    'description' => 'Stub',
                ],
                'enabled'          => [
                    'type'        => Type::boolean(),
                    'description' => 'Stub',
                ],
                'moduleName'      => [
                    'type'        => $this->app[ModuleNameType::class],
                    'description' => 'Module name',
                ],
                'fromMarketplace' => [
                    'type'        => Type::boolean(),
                    'description' => 'Flag',
                ],
                'iconURL'         => [
                    'type'        => $this->app[UrlType::class],
                    'description' => 'Method icon URL',
                ],
                'modulePageURL'  => [
                    'type'        => $this->app[UrlType::class],
                    'description' => 'Market module page URL',
                ],
            ],
        ];
    }
}
