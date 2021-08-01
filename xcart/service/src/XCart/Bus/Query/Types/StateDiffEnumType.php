<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types;

use GraphQL\Type\Definition\EnumType;
use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class StateDiffEnumType extends EnumType
{
    use ObjectTypeTrait;

    protected function defineConfig()
    {
        return [
            'name'        => 'StateTransition',
            'description' => 'Module state transition on rebuild',
            'values'      => [
                'enable'           => [
                    'value'       => ChangeUnitProcessor::TRANSITION_ENABLE,
                    'description' => 'Module was enabled on rebuild.',
                ],
                'disable'          => [
                    'value'       => ChangeUnitProcessor::TRANSITION_DISABLE,
                    'description' => 'Module was disabled on rebuild.',
                ],
                'install_disabled' => [
                    'value'       => ChangeUnitProcessor::TRANSITION_INSTALL_DISABLED,
                    'description' => 'Module was installed in disabled state on rebuild.',
                ],
                'install_enabled'  => [
                    'value'       => ChangeUnitProcessor::TRANSITION_INSTALL_ENABLED,
                    'description' => 'Module was installed in enabled state on rebuild.',
                ],
                'remove'           => [
                    'value'       => ChangeUnitProcessor::TRANSITION_REMOVE,
                    'description' => 'Module was removed on rebuild.',
                ],
                'upgrade'          => [
                    'value'       => ChangeUnitProcessor::TRANSITION_UPGRADE,
                    'description' => 'Module was upgraded',
                ],
            ],
        ];
    }
}
