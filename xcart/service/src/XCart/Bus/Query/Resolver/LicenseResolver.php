<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use XCart\Bus\Client\LicenseClient;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\Bus\Domain\Module;
use XCart\Bus\Exception\MarketplaceException;
use XCart\Bus\Helper\UrlBuilder;
use XCart\Bus\Query\Context;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Query\Data\Flatten\Flatten;
use XCart\Bus\Query\Data\LicenseDataSource;
use XCart\Bus\Query\Data\MarketplaceModulesDataSource;
use XCart\Bus\Query\Data\MarketplaceShopAdapter;
use XCart\Bus\Query\Data\ModulesDataSource;
use XCart\Bus\Query\Data\ScenarioDataSource;
use XCart\Bus\Query\Data\SetDataSource;
use XCart\Bus\Query\Data\WavesDataSource;
use XCart\Bus\Query\Types\Output\AlertType;
use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;
use XCart\Bus\Rebuild\Scenario\ScenarioRule\ScenarioRuleException;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class LicenseResolver
{
    /**
     * @var LicenseDataSource
     */
    private $licenseDataSource;

    /**
     * @var LicenseClient
     */
    private $licenseClient;

    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * @var WavesDataSource
     */
    private $wavesDataSource;

    /**
     * @var ModulesDataSource
     */
    private $modulesDataSource;

    /**
     * @var MarketplaceModulesDataSource
     */
    private $marketplaceModulesDataSource;

    /**
     * @var ScenarioDataSource
     */
    private $scenarioDataSource;

    /**
     * @var SetDataSource
     */
    private $setDataSource;

    /**
     * @var ChangeUnitProcessor
     */
    private $changeUnitProcessor;

    /**
     * @var RebuildResolver
     */
    private $rebuildResolver;

    /**
     * @var ModulesResolver
     */
    private $modulesResolver;

    /**
     * @var MarketplaceShopAdapter
     */
    private $marketplaceShopAdapter;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @param LicenseDataSource            $licenseDataSource
     * @param LicenseClient                $licenseClient
     * @param CoreConfigDataSource         $coreConfigDataSource
     * @param WavesDataSource              $wavesDataSource
     * @param ModulesDataSource            $modulesDataSource
     * @param MarketplaceModulesDataSource $marketplaceModulesDataSource
     * @param ScenarioDataSource           $scenarioDataSource
     * @param SetDataSource                $setDataSource
     * @param ChangeUnitProcessor          $changeUnitProcessor
     * @param RebuildResolver              $rebuildResolver
     * @param ModulesResolver              $modulesResolver
     * @param MarketplaceShopAdapter       $marketplaceShopAdapter
     * @param UrlBuilder                   $urlBuilder
     */
    public function __construct(
        LicenseDataSource $licenseDataSource,
        LicenseClient $licenseClient,
        CoreConfigDataSource $coreConfigDataSource,
        WavesDataSource $wavesDataSource,
        ModulesDataSource $modulesDataSource,
        MarketplaceModulesDataSource $marketplaceModulesDataSource,
        ScenarioDataSource $scenarioDataSource,
        SetDataSource $setDataSource,
        ChangeUnitProcessor $changeUnitProcessor,
        RebuildResolver $rebuildResolver,
        ModulesResolver $modulesResolver,
        MarketplaceShopAdapter $marketplaceShopAdapter,
        UrlBuilder $urlBuilder
    ) {
        $this->licenseDataSource            = $licenseDataSource;
        $this->licenseClient                = $licenseClient;
        $this->coreConfigDataSource         = $coreConfigDataSource;
        $this->wavesDataSource              = $wavesDataSource;
        $this->modulesDataSource            = $modulesDataSource;
        $this->scenarioDataSource           = $scenarioDataSource;
        $this->setDataSource = $setDataSource;
        $this->changeUnitProcessor          = $changeUnitProcessor;
        $this->rebuildResolver              = $rebuildResolver;
        $this->marketplaceModulesDataSource = $marketplaceModulesDataSource;
        $this->modulesResolver              = $modulesResolver;
        $this->marketplaceShopAdapter       = $marketplaceShopAdapter;
        $this->urlBuilder                   = $urlBuilder;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param             $value
     * @param             $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return array
     *
     * @Resolver()
     */
    public function getList($value, $args, Context $context, ResolveInfo $info): array
    {
        if (!($context->mode & Context::ACCESS_MODE_READ_LICENSE)) {
            return [];
        }

        return $this->licenseDataSource->getAll();
    }

    /**
     * @param array $licenses
     *
     * @return array
     *
     * @Resolver()
     */
    public function getListWithExtraData($value, $args, Context $context, ResolveInfo $info): array
    {
        $licenses = $this->getList($value, $args, $context, $info);

        if (!$licenses) {
            return [];
        }

        $modulesDataSource = $this->modulesDataSource;

        $coreMajorVersion = '';
        $coreVersion      = $this->coreConfigDataSource->version;

        if (preg_match('/(\d+\.\d+)\.(\d+)\.(\d+)/', $coreVersion, $matches)) {
            $coreMajorVersion = $matches[1];
        }

        return array_map(
            static function ($license) use ($modulesDataSource, $coreMajorVersion) {
                $license['hasActualVersion'] = true;
                $license['isInstalled']      = false;

                if ($coreMajorVersion) {
                    $moduleId = "{$license['author']}-{$license['name']}";
                    $modules  = $modulesDataSource->find($moduleId);

                    foreach ($modules as $module) {
                        if ($module->installed) {
                            $license['isInstalled'] = true;
                        } else {
                            $moduleMajorVersion = '';
                            if (preg_match('/(\d+\.\d+)\.(\d+)\.(\d+)/', $module->version, $matches)) {
                                $moduleMajorVersion = $matches[1];
                            }

                            if ($moduleMajorVersion && $moduleMajorVersion !== $coreMajorVersion) {
                                $license['hasActualVersion'] = false;
                            }
                        }
                    }
                }

                return $license;
            },
            $licenses
        );
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param             $value
     * @param array       $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return array|null
     *
     * @Resolver()
     */
    public function resolveCoreLicense($value, $args, Context $context, ResolveInfo $info): ?array
    {
        if (!($context->mode & Context::ACCESS_MODE_READ_LICENSE)) {
            return null;
        }

        $license = $this->licenseDataSource->findBy([
            'author' => 'CDev',
            'name'   => 'Core',
            'active' => true,
        ]);

        return $license ?: null;
    }


    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param             $value
     * @param array       $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return array
     *
     * @Resolver()
     */
    public function getCoreLicenseGeneralInfo($value, $args, Context $context, ResolveInfo $info): array
    {
        $license = $this->licenseDataSource->findBy([
            'author' => 'CDev',
            'name'   => 'Core',
            'active' => true,
        ]);

        return [
            'editionName'    => $license['keyData']['editionName'] ?? null,
            'xcnPlan' => $license['xcnPlan'] ? (int) $license['xcnPlan'] : null,
        ] ;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return array
     * @throws Exception
     *
     * @Resolver()
     */
    public function register($value, $args, $context, ResolveInfo $info): array
    {
        if (!($context->mode & Context::ACCESS_MODE_WRITE)) {
            return [];
        }

        $result = [];

        $key = [];
        try {
            if (!empty($args['key'])) {
                $key = $this->registerLicense(trim($args['key']));
            }
        } catch (MarketplaceException $exception) {
            $result['alert'][] = [
                'type'    => 'danger',
                'message' => 'activate_license_dialog.result.invalid',
                'params'  => AlertType::prepareParams([
                    'action'  => $exception->getMessage(),
                    'code'    => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ]),
            ];
        }

        if ($key) {
            $result['key'] = $key['keyValue'];

            try {
                $scenario = $this->processKeyInfo($key, $context);
            } catch (ScenarioRuleException $exception) {
                $scenario          = null;
                $result['alert'][] = [
                    'type'    => 'warning',
                    'message' => $exception->getMessage(),
                    'params'  => AlertType::prepareParams($exception->getParams()),
                ];
            } catch (Exception $exception) {
                $scenario          = null;
                $result['alert'][] = [
                    'type'    => 'warning',
                    'message' => $exception->getMessage(),
                ];
            }

            if ($scenario) {
                $state = $this->rebuildResolver->startRebuild(
                    null,
                    [
                        'id'     => $scenario['id'],
                        'reason' => 'module-state',
                    ],
                    $context,
                    new ResolveInfo([])
                );

                $result['action'] = 'rebuild/' . $state->id;

            } else {
                if ((int) $key['keyType'] === 2) {
                    $result['alert'][] = [
                        'type'    => 'success',
                        'message' => 'activate_license_dialog.result.success.core',
                    ];
                } else {
                    /** @var Module $module */
                    $module = $this->modulesDataSource->findOne(Module::buildModuleId($key['author'], $key['name']));

                    $result['alert'][] = [
                        'type'    => 'success',
                        'message' => 'activate_license_dialog.result.success.module',
                        'params'  => AlertType::prepareParams([
                            'name'   => $module->moduleName,
                            'author' => $module->authorName,
                        ]),
                    ];

                    if (!empty($key['keyData']['expDate'])
                        && $key['keyData']['expDate'] < time()
                    ) {
                        $marketplaceShop = $this->marketplaceShopAdapter->get();

                        $result['alert'][] = [
                            'type'    => 'danger',
                            'message' => 'module_state_message.license_expired',
                            'params'  => AlertType::prepareParams([
                                'renewUrl' => $marketplaceShop->getRenewalURL($key['keyData']['prolongKey'], $key['keyValue'], $this->urlBuilder->buildServiceMainUrl('afterPurchase')),
                            ]),
                        ];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return string
     * @throws Exception
     *
     * @Resolver()
     */
    public function getRenewLicensesUrl($value, $args, $context, ResolveInfo $info): string
    {
        $expiredLicenses = [];
        foreach ($this->licenseDataSource->getAll() as $license) {
            $expiration = $license['keyData']['expDate'] ?? null;
            if ($expiration && $expiration < time()) {
                $expiredLicenses[] = [
                    'keyValue'   => $license['keyValue'],
                    'prolongKey' => $license['keyData']['prolongKey'],
                ];
            }
        }

        if ($expiredLicenses) {
            $marketplaceShop = $this->marketplaceShopAdapter->get();

            return $marketplaceShop->getRenewalAllURL($expiredLicenses, $this->urlBuilder->buildServiceMainUrl('afterPurchase'));
        }

        return '';
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return bool
     * @throws Exception
     *
     * @Resolver()
     */
    public function clearCache($value, $args, $context, ResolveInfo $info): bool
    {
        if (!($context->mode === Context::ACCESS_MODE_FULL)) {
            return false;
        }

        $time = time();

        $this->coreConfigDataSource->dataDate                 = 0;
        $this->coreConfigDataSource->cacheDate                = 0;
        $this->coreConfigDataSource->shippingMethodsCacheDate = $time;
        $this->coreConfigDataSource->paymentMethodsCacheDate  = $time;

        $this->marketplaceModulesDataSource->clear();
        $this->setDataSource->clear();

        return true;
    }

    /**
     * @param string $licenseKey
     *
     * @return array
     * @throws MarketplaceException
     */
    private function registerLicense($licenseKey): array
    {
        $keys = $this->licenseClient->registerLicenseKey($licenseKey, $this->getCurrentWave());
        if ($keys) {
            $key             = array_shift($keys);
            $key['keyValue'] = $licenseKey;
            $key['active']   = true;

            return $key;
        }

        return [];
    }

    /**
     * @return int|null
     */
    private function getCurrentWave(): ?int
    {
        $wave  = (int) $this->coreConfigDataSource->wave;
        $waves = $this->wavesDataSource->getAll();

        if (isset($waves[$wave])) {
            $waveKeys = array_keys($waves);
            $lastKey  = (int) array_pop($waveKeys);
            if ($lastKey === $wave) {
                return null;
            }
        } else {
            return null;
        }

        return $wave;
    }

    /**
     * @param $keyInfo
     * @param Context $context
     *
     * @return array|null
     * @throws ScenarioRuleException
     * @throws Exception
     */
    private function processKeyInfo($keyInfo, Context $context): ?array
    {
        if (!empty($keyInfo['keyData']['wave'])) {
            $this->coreConfigDataSource->wave = $keyInfo['keyData']['wave'];
        }

        $this->marketplaceModulesDataSource->clear();
        $this->licenseDataSource->removePending($keyInfo);

        if ($this->licenseDataSource->isCoreKey($keyInfo)) {
            $this->licenseDataSource->saveOne($keyInfo);

            if ($this->licenseDataSource->isFreeCoreKey($keyInfo)) {
                return $this->generateScenarioForFreeLicenseKey();
            }
        } else {
            $this->licenseDataSource->saveOne($keyInfo);

            return $this->generateScenarioForModuleKey($keyInfo, $context);
        }

        return null;
    }

    /**
     * @return array|null
     * @throws ScenarioRuleException
     * @throws Exception
     */
    private function generateScenarioForFreeLicenseKey(): ?array
    {
        /** @var Module[] $freeModules */
        $freeModules = $this->modulesDataSource->getSlice(
            Flatten::RULE_LAST,
            [
                'installed'      => true,
                'nonFreeEdition' => true,
            ]
        );

        $changeUnits = [];
        foreach ($freeModules as $module) {
            $changeUnits[] = [
                'id'     => $module->id,
                'remove' => true,
            ];
        }

        if (!$changeUnits) {
            return null;
        }

        $scenario = $this->changeUnitProcessor->process($this->scenarioDataSource->startEmptyScenario(), $changeUnits);

        //$scenario['id']   = uniqid('scenario', true);
        //$scenario['type'] = 'common';
        //$scenario['date'] = time();

        $this->scenarioDataSource->saveOne($scenario);

        return $scenario;
    }

    /**
     * @param array $keyInfo
     * @param Context $context
     *
     * @return array|null
     * @throws ScenarioRuleException
     * @throws Exception
     */
    private function generateScenarioForModuleKey($keyInfo, Context $context): ?array
    {
        /** @var Module $module */
        $module = $this->modulesDataSource->findOne(
            Module::buildModuleId($keyInfo['author'], $keyInfo['name'])
        );

        if ($module->installed && $module->enabled) {
            return null;
        }

        if ($module->installed) {
            return null;
        }

        $resolverModule = $this->modulesResolver->getModule($module->id, $context);
        if (isset($resolverModule) && empty($resolverModule['actions']['install'])) {
            return null;
        }

        $changeUnits = [
            [
                'id'      => $module->id,
                'install' => true,
                'version' => $module->version,
            ],
        ];

        $scenario = $this->changeUnitProcessor->process($this->scenarioDataSource->startEmptyScenario(), $changeUnits);

        $this->scenarioDataSource->saveOne($scenario);

        return $scenario;
    }
}
