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
class ModuleActionsType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'name'   => 'ModuleActions',
            'fields' => [
                'enable'         => Type::boolean(),
                'disable'        => Type::boolean(),
                'switch'         => Type::boolean(),
                'remove'         => Type::boolean(),
                'install'        => Type::boolean(),
                'installDemo'    => Type::boolean(),
                'settings'       => Type::boolean(),
                'purchase'       => Type::boolean(),
                'purchaseDemo'   => Type::boolean(),
                'pack'           => Type::boolean(),
                'upgradeRequest' => Type::boolean(),
                'manageLayout'   => Type::boolean(),
            ],
        ];
    }
}
