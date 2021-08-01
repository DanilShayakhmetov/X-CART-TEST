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
class EditionType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'fields' => [
                'name'           => Type::string(),
                'xb_product_id'  => Type::string(),
                'is_cloud'       => Type::boolean(),
                'description'    => Type::string(),
                'price'          => Type::string(),
                'avail_for_sale' => Type::boolean(),
                'xcnPlan'        => Type::int(),
                'purchase_url'   => Type::string(),
            ],
        ];
    }
}
