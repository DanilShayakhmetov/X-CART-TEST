<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types;

use GraphQL\Type\Definition\Type;
use XCart\Bus\Query\Resolver\BannersResolver;
use XCart\Bus\Query\Resolver\EditionsResolver;
use XCart\Bus\Query\Resolver\GDPRModulesResolver;
use XCart\Bus\Query\Resolver\LanguageDataResolver;
use XCart\Bus\Query\Resolver\LicenseResolver;
use XCart\Bus\Query\Resolver\LicenseTextResolver;
use XCart\Bus\Query\Resolver\ModulesResolver;
use XCart\Bus\Query\Resolver\NotificationsResolver;
use XCart\Bus\Query\Resolver\PaymentMethodsResolver;
use XCart\Bus\Query\Resolver\RebuildResolver;
use XCart\Bus\Query\Resolver\RecommendedModulesResolver;
use XCart\Bus\Query\Resolver\ScenarioResolver;
use XCart\Bus\Query\Resolver\SegmentResolver;
use XCart\Bus\Query\Resolver\SectionsResolver;
use XCart\Bus\Query\Resolver\ShippingMethodsResolver;
use XCart\Bus\Query\Resolver\SystemDataResolver;
use XCart\Bus\Query\Resolver\TagsResolver;
use XCart\Bus\Query\Resolver\TrialResolver;
use XCart\Bus\Query\Resolver\UpgradeResolver;
use XCart\Bus\Query\Resolver\WavesResolver;
use XCart\Bus\Query\Types\Output\AvailableUpgradeType;
use XCart\Bus\Query\Types\Output\BannerType;
use XCart\Bus\Query\Types\Output\CoreLicenseGeneralInfoType;
use XCart\Bus\Query\Types\Output\EditionType;
use XCart\Bus\Query\Types\Output\InstallationData;
use XCart\Bus\Query\Types\Output\IntegrityCheckCacheType;
use XCart\Bus\Query\Types\Output\LanguageMessageType;
use XCart\Bus\Query\Types\Output\LicensesTextType;
use XCart\Bus\Query\Types\Output\LicenseType;
use XCart\Bus\Query\Types\Output\ModulesPageType;
use XCart\Bus\Query\Types\Output\ModuleType;
use XCart\Bus\Query\Types\Output\NotificationType;
use XCart\Bus\Query\Types\Output\PageParamType;
use XCart\Bus\Query\Types\Output\PaymentMethodType;
use XCart\Bus\Query\Types\Output\RebuildStateType;
use XCart\Bus\Query\Types\Output\RecommendedModulesType;
use XCart\Bus\Query\Types\Output\ScenarioType;
use XCart\Bus\Query\Types\Output\SegmentType;
use XCart\Bus\Query\Types\Output\SectionsType;
use XCart\Bus\Query\Types\Output\ShippingMethodType;
use XCart\Bus\Query\Types\Output\SystemData;
use XCart\Bus\Query\Types\Output\TagType;
use XCart\Bus\Query\Types\Output\SkinData;
use XCart\Bus\Query\Types\Output\TrialType;
use XCart\Bus\Query\Types\Output\UpgradeEntryType;
use XCart\Bus\Query\Types\Output\WaveType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class QueryType extends AObjectType
{
    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'name'   => 'Query',
            'fields' => [
                'availableUpgradeTypes'   => [
                    'type'        => Type::listOf($this->app[AvailableUpgradeType::class]),
                    'description' => 'Returns list of available upgrade types',
                    'resolve'     => $this->app[UpgradeResolver::class . ':getAvailableUpgradeTypes'],
                ],
                'banners'                 => [
                    'type'    => Type::listOf($this->app[BannerType::class]),
                    'resolve' => $this->app[BannersResolver::class . ':getList'],
                ],
                'tags'                    => [
                    'type'    => Type::listOf($this->app[TagType::class]),
                    'resolve' => $this->app[TagsResolver::class . ':getList'],
                ],
                'modulesPage'             => [
                    'type'        => $this->app[ModulesPageType::class],
                    'description' => 'Returns list of modules',
                    'resolve'     => $this->app[ModulesResolver::class . ':resolvePage'],
                    'args'        => [
                        'language'       => Type::string(),
                        'version'        => Type::string(),
                        'search'         => Type::string(),
                        'installed'      => Type::boolean(),
                        'custom'         => Type::boolean(),
                        'private'        => Type::boolean(),
                        'canInstall'     => Type::boolean(),
                        'system'         => Type::boolean(),
                        'type'           => Type::string(),
                        'enabled'        => Type::string(),
                        'payable'        => Type::string(),
                        'tag'            => Type::string(),
                        'nonFreeEdition' => Type::boolean(),
                        'licensed'       => Type::boolean(),
                        'sort'           => Type::listOf(Type::string()),
                        'limit'          => Type::listOf(Type::int()),
                        'scenario'       => Type::id(),
                        'isSalesChannel' => Type::boolean(),
                        'isLanding'      => Type::boolean(),
                        'includeIds'     => Type::listOf(Type::id()),
                        'integrityCheck' => Type::boolean(),
                        'existsUpdate'   => Type::string(),
                        'onlyAvailable'  => Type::boolean(),
                    ],
                ],
                'module'                  => [
                    'type'    => $this->app[ModuleType::class],
                    'resolve' => $this->app[ModulesResolver::class . ':resolveModule'],
                    'args'    => [
                        'id'       => Type::id(),
                        'language' => Type::string(),
                    ],
                ],
                'notifications'           => [
                    'type'    => Type::listOf($this->app[NotificationType::class]),
                    'resolve' => $this->app[NotificationsResolver::class . ':getList'],
                    'args'    => [
                        'type'   => Type::string(),
                        'target' => Type::string(),
                        'page'   => Type::string(),
                    ],
                ],
                'ongoing'                 => [
                    'type'        => Type::listOf($this->app[RebuildStateType::class]),
                    'description' => 'Global application state',
                    'resolve'     => $this->app[RebuildResolver::class . ':ongoingScripts'],
                ],
                'upgradeEntriesCount'     => [
                    'type'        => Type::int(),
                    'description' => 'Returns count of available upgrades for modules',
                    'args'        => [
                        'type' => [
                            'type'        => Type::nonNull(Type::string()),
                            'description' => 'Upgrade type',
                        ],
                    ],
                    'resolve'     => $this->app[UpgradeResolver::class . ':getUpgradeEntriesCount'],
                ],
                'payment_methods'         => [
                    'type'    => Type::listOf($this->app[PaymentMethodType::class]),
                    'args'    => [
                        'countryCode' => Type::string(),
                    ],
                    'resolve' => $this->app[PaymentMethodsResolver::class . ':getList'],
                ],
                'gdpr_modules'            => [
                    'type'    => Type::listOf($this->app[ModuleType::class]),
                    'resolve' => $this->app[GDPRModulesResolver::class . ':getList'],
                ],
                'scenario'                => [
                    'type'        => $this->app[ScenarioType::class],
                    'description' => 'Scenario',
                    'args'        => [
                        'id'       => [
                            'type'        => Type::id(),
                            'description' => 'Scenario id',
                        ],
                        'language' => [
                            'type'        => Type::string(),
                            'description' => 'Locale to output',
                        ],
                    ],
                    'resolve'     => $this->app[ScenarioResolver::class . ':find'],
                ],
                'shipping_methods'        => [
                    'type'    => Type::listOf($this->app[ShippingMethodType::class]),
                    'resolve' => $this->app[ShippingMethodsResolver::class . ':getList'],
                ],
                'upgradeList'             => [
                    'type'        => Type::listOf($this->app[UpgradeEntryType::class]),
                    'description' => 'Returns list of upgrade entries of specified type',
                    'args'        => [
                        'type'     => [
                            'type'        => Type::nonNull(Type::string()),
                            'description' => 'Upgrade type',
                        ],
                        'moduleId' => [
                            'type' => Type::id(),
                        ],
                    ],
                    'resolve'     => $this->app[UpgradeResolver::class . ':resolveList'],
                ],
                'waves'                   => [
                    'type'    => Type::listOf($this->app[WaveType::class]),
                    'resolve' => $this->app[WavesResolver::class . ':getList'],
                ],
                'licenses'                => [
                    'type'    => Type::listOf($this->app[LicenseType::class]),
                    'resolve' => $this->app[LicenseResolver::class . ':getListWithExtraData'],
                ],
                'renewLicensesUrl'        => [
                    'type'    => Type::string(),
                    'resolve' => $this->app[LicenseResolver::class . ':getRenewLicensesUrl'],
                ],
                'trial'                   => [
                    'type'    => $this->app[TrialType::class],
                    'resolve' => $this->app[TrialResolver::class . ':resolveTrialData'],
                ],
                'coreLicense'             => [
                    'type'    => $this->app[LicenseType::class],
                    'resolve' => $this->app[LicenseResolver::class . ':resolveCoreLicense'],
                ],
                'coreLicenseGeneralInfo'  => [
                    'type'    => $this->app[CoreLicenseGeneralInfoType::class],
                    'resolve' => $this->app[LicenseResolver::class . ':getCoreLicenseGeneralInfo'],
                ],
                'installationData'        => [
                    'type'    => $this->app[InstallationData::class],
                    'resolve' => $this->app[SystemDataResolver::class . ':resolveInstallationData'],
                ],
                'systemData'              => [
                    'type'    => $this->app[SystemData::class],
                    'resolve' => $this->app[SystemDataResolver::class . ':resolveSystemData'],
                ],
                'skinData'                => [
                    'type'    => $this->app[SkinData::class],
                    'resolve' => $this->app[SystemDataResolver::class . ':resolveSkinData'],
                ],
                'marketplaceState'        => [
                    'type'    => $this->app[SystemData::class],
                    'resolve' => $this->app[SystemDataResolver::class . ':resolveMarketplaceState'],
                    'args'    => [
                        'force' => [
                            'type'         => Type::boolean(),
                            'description'  => 'Force marketplace lock state check',
                            'defaultValue' => false,
                        ],
                    ],
                ],
                'storefrontStatus'        => [
                    'type'    => $this->app[SystemData::class],
                    'resolve' => $this->app[SystemDataResolver::class . ':resolveStorefrontStatus'],
                ],
                'languages'               => [
                    'type'    => Type::listOf(Type::string()),
                    'resolve' => $this->app[LanguageDataResolver::class . ':getLanguages'],
                ],
                'languageMessages'        => [
                    'type'    => Type::listOf($this->app[LanguageMessageType::class]),
                    'resolve' => $this->app[LanguageDataResolver::class . ':getLanguageMessages'],
                    'args'    => [
                        'code' => [
                            'type'        => Type::id(),
                            'description' => 'Language code',
                        ],
                    ],
                ],
                'rebuildState'            => [
                    'type'    => $this->app[RebuildStateType::class],
                    'args'    => [
                        'id' => Type::id(),
                    ],
                    'resolve' => $this->app[RebuildResolver::class . ':find'],
                ],
                'integrityCheckCache'     => [
                    'type'        => $this->app[IntegrityCheckCacheType::class],
                    'description' => 'Returns integrity check state cache',
                    'resolve'     => $this->app[ModulesResolver::class . ':getIntegrityCheckCache'],
                ],
                'licensesText'            => [
                    'type'    => Type::listOf($this->app[LicensesTextType::class]),
                    'args'    => [
                        'modulesId' => Type::listOf(Type::string()),
                    ],
                    'resolve' => $this->app[LicenseTextResolver::class . ':getLicensesText'],
                ],
                'sections'                => [
                    'type'    => Type::listOf($this->app[SectionsType::class]),
                    'resolve' => $this->app[SectionsResolver::class . ':getList'],
                ],
                'recommendedModules'      => [
                    'type'    => Type::listOf($this->app[RecommendedModulesType::class]),
                    'resolve' => $this->app[RecommendedModulesResolver::class . ':getList'],
                    'args'    => [
                        'pageParams' => Type::listOf($this->app[PageParamType::class]),
                        'limit'      => Type::int(),
                    ],
                ],
                'editions'                => [
                    'type'    => Type::listOf($this->app[EditionType::class]),
                    'resolve' => $this->app[EditionsResolver::class . ':getList'],
                ],
                'segment'                 => [
                    'type'    => $this->app[SegmentType::class],
                    'resolve' => $this->app[SegmentResolver::class . ':getData'],
                ],
                'isCancelledSubscription' => [
                    'type'    => Type::boolean(),
                    'resolve' => $this->app[EditionsResolver::class . ':isCancelledSubscription'],
                ],
            ],
        ];
    }
}
