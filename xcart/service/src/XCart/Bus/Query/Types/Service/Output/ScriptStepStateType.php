<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types\Service\Output;

use GraphQL\Type\Definition\Type;
use XCart\Bus\Query\Types\AObjectType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ScriptStepStateType extends AObjectType
{
    protected function defineConfig()
    {
        return [
            'fields' => [
                'id'        => Type::id(),
                'index'     => Type::int(),
                'status'    => Type::string(),
                'timeStart' => Type::int(),
                'timeEnd'   => Type::int(),
                'error'     => Type::string(),
            ],
        ];
    }
}