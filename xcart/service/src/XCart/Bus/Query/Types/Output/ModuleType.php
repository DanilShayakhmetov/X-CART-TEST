<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types\Output;

use GraphQL\Type\Definition\Type;
use XCart\Bus\Query\Resolver\ModulesResolver;
use XCart\Bus\Query\Types\AObjectType;
use XCart\Bus\Query\Types\Scalar\VersionType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ModuleType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig(): array
    {
        return [
            'name'        => 'Module',
            'description' => 'X-Cart module',
            'fields'      => [
                'id'               => Type::id(),
                'version'          => $this->app[VersionType::class],
                'installedVersion' => $this->app[VersionType::class],
                'author'           => Type::string(),
                'name'             => Type::string(),
                'authorName'       => Type::string(),
                'moduleName'       => Type::string(),
                'description'      => Type::string(),
                'dependsOn'        => Type::listOf(Type::id()),
                'incompatibleWith' => Type::listOf(Type::id()),
                'requiredBy'       => Type::listOf(Type::id()),
                'isSystem'         => Type::boolean(),
                'icon'             => Type::string(),
                'listIcon'         => Type::string(),
                'installed'        => Type::boolean(),
                'installedDate'    => Type::string(),
                'enabled'          => Type::boolean(),
                'skinPreview'      => Type::string(),
                'pageUrl'          => Type::string(),
                'authorPageUrl'    => Type::string(),
                'authorEmail'      => Type::string(),
                'revisionDate'     => Type::string(),
                'price'            => Type::float(),
                'origPrice'        => Type::float(),
                'onSale'           => Type::boolean(),
                'downloads'        => Type::int(),
                'rating'           => Type::float(),
                'tags'             => Type::listOf($this->app[ModuleTagType::class]),
                'salesChannelPos'  => Type::string(),
                'isLanding'        => Type::boolean(),
                'landingPosition'  => Type::string(),
                'actions'          => $this->app[ModuleActionsType::class],
                'scenarioState'    => $this->app[ModuleStateType::class],
                'purchaseUrl'      => Type::string(),
                'license'          => Type::string(),
                'hasLicense'       => Type::boolean(),
                'messages'         => Type::listOf($this->app[AlertType::class]),
                'wave'             => Type::string(),
                'edition'          => Type::string(),
                'editions'         => Type::listOf(Type::string()),
                'expiration'       => Type::string(),
                'xbProductId'      => Type::string(),

                'integrityViolations'      => [
                    'type'    => $this->app[ModuleIntegrityViolationsType::class],
                    'args'    => [
                        'limit' => Type::listOf(Type::int()),
                    ],
                    'resolve' => $this->app[ModulesResolver::class . ':getIntegrityViolations'],
                ],
                'integrityViolationsCache' => $this->app[ModuleIntegrityViolationsType::class],
                'changelog'                => Type::listOf(Type::string()),
                'type'                => Type::string(),
            ],
        ];
    }
}
