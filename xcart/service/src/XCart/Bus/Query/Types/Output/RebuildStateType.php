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
class RebuildStateType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'fields' => [
                'id'                    => [
                    'type' => Type::id(),
                ],
                'type'                  => [
                    'type' => Type::string(),
                ],
                'reason'                => [
                    'type' => Type::string(),
                ],
                'canRollback'           => [
                    'type' => Type::boolean(),
                ],
                'progressMax'           => [
                    'type'        => Type::int(),
                    'description' => 'Progress bar max value',
                ],
                'progressValue'         => [
                    'type'        => Type::int(),
                    'description' => 'Progress bar current value',
                ],
                'errorType'             => [
                    'type'        => Type::string(),
                    'description' => 'Error type',
                ],
                'errorData'             => [
                    'type'        => Type::string(),
                    'description' => 'Error data',
                ],
                'errorTitle'            => [
                    'type'        => Type::string(),
                    'description' => 'General error message',
                ],
                'errorDescription'      => [
                    'type'        => Type::string(),
                    'description' => 'Detailed error description and steps to resolving the problem',
                ],
                'prompts'          => [
                    'type'        => Type::listOf(Type::string()),
                    'description' => 'Prompts list',
                ],
                'state'                 => [
                    'type'        => Type::string(),
                    'description' => 'Script state',
                ],
                'currentStepInfo'       => Type::listOf($this->app[RebuildStateInfo::class]),
                'finishedStepInfo'      => Type::listOf($this->app[RebuildStateInfo::class]),
                'returnUrl'             => [
                    'type'        => Type::string(),
                    'description' => 'URL to redirect to after successful script execution',
                ],
                'failureReturnUrl'      => [
                    'type'        => Type::string(),
                    'description' => 'URL to redirect to after unsuccessful script execution',
                ],
                'gaData'                => Type::listOf(Type::string()),
                'hasEnabledTransitions' => [
                    'type' => Type::boolean(),
                ],
                'modulesWithSettings' => [
                    'type' => Type::listOf(Type::listOf(Type::string())),
                ],
            ],
        ];
    }
}
