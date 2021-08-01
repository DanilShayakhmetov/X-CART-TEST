<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types\Output;

use GraphQL\Type\Definition\Type;
use XCart\Bus\Query\Types\AObjectType;
use XCart\Bus\Query\Types\Scalar\WordType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ShippingTranslationType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'fields' => [
                'code' => [
                    'type'        => $this->app[WordType::class], // @todo: scalar language code,
                    'description' => 'Language code',
                ],
                'name' => [
                    'type'        => Type::string(),
                    'description' => 'Shipping method name',
                ],
            ],
        ];
    }
}
