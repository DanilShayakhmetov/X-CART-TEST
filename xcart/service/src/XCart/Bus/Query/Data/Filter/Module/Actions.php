<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use Iterator;
use XCart\Bus\Domain\Module;
use XCart\Bus\Editions\Core\UninstallAvailDecider;
use XCart\Bus\Helper\UrlBuilder;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Query\Data\Filter\AModifier;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Query\Data\LicenseDataSource;
use XCart\Bus\Query\Data\ModulesDataSource;
use XCart\Bus\Query\Types\Output\AlertType;
use XCart\MarketplaceShop;

class Actions extends AModifier
{
    /**
     * @var string
     */
    private $coreMajorVersion;

    /**
     * @var int
     */
    private $coreMinorVersion;

    /**
     * @var ModulesDataSource
     */
    private $modulesDataSource;

    /**
     * @var array
     */
    private $licenses;

    /**
     * @var UninstallAvailDecider
     */
    private $uninstallAvailDecider;

    /**
     * @var MarketplaceShop
     */
    private $marketplaceShop;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var bool
     */
    private $developerMode;

    /**
     * @var array|null
     */
    private $coreLicense;

    /**
     * @var array
     */
    private $moduleLicense = [];

    /**
     * @var array
     */
    private $requestForUpgrade;

    /**
     * @var int
     */
    private $time;

    /**
     * @var bool
     */
    private $displayUpdateNotification;

    /**
     * @var bool
     */
    private $isCloud;

    /**
     * @param Iterator                   $iterator
     * @param string                     $field
     * @param mixed                      $data
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param ModulesDataSource          $modulesDataSource
     * @param CoreConfigDataSource       $coreConfigDataSource
     * @param LicenseDataSource          $licenseDataSource
     * @param UninstallAvailDecider      $uninstallAvailDecider
     * @param MarketplaceShop            $marketplaceShop
     * @param UrlBuilder                 $urlBuilder
     * @param bool                       $developerMode
     * @param bool                       $displayUpdateNotification
     * @param bool                       $isCloud
     */
    public function __construct(
        Iterator $iterator,
        $field,
        $data,
        InstalledModulesDataSource $installedModulesDataSource,
        ModulesDataSource $modulesDataSource,
        CoreConfigDataSource $coreConfigDataSource,
        LicenseDataSource $licenseDataSource,
        UninstallAvailDecider $uninstallAvailDecider,
        MarketplaceShop $marketplaceShop,
        UrlBuilder $urlBuilder,
        $developerMode,
        $displayUpdateNotification,
        $isCloud
    ) {
        parent::__construct($iterator, $field, $data);

        /** @var Module $core */
        $core = $installedModulesDataSource->find('CDev-Core');

        $this->coreMajorVersion          = $this->getMajorVersion($core->version);
        $this->coreMinorVersion          = $this->getMinorVersion($core->version);
        $this->modulesDataSource         = $modulesDataSource;
        $this->licenses                  = $licenseDataSource->getAll();
        $this->uninstallAvailDecider     = $uninstallAvailDecider;
        $this->marketplaceShop           = $marketplaceShop;
        $this->urlBuilder                = $urlBuilder;
        $this->developerMode             = $developerMode;
        $this->displayUpdateNotification = $displayUpdateNotification;
        $this->isCloud                   = $isCloud;

        $this->coreLicense       = $this->getLicense($core);
        $this->requestForUpgrade = $coreConfigDataSource->requestForUpgrade;
        $this->time              = time();
    }

    /**
     * @return mixed
     */
    public function current()
    {
        /** @var Module $item */
        $item = $this->getInnerIterator()->current();

        $isCore = $item->id === 'CDev-Core';

        $actions = [];

        $actions['switch']         = true;
        $actions['remove']         = true;
        $actions['install']        = false;
        $actions['purchase']       = false;
        $actions['upgradeRequest'] = false;
        $actions['manageLayout']   = false;

        $actions['pack'] = $this->developerMode;

        $messages = [];

        $license = $this->getLicense($item);

        if ($item['enabled']) {
            $actions['install'] = false;

            if (!$item['canDisable']) {
                $actions['switch'] = false;
                if (!$isCore) {
                    $messages[] = [
                        'type'    => 'warning',
                        'message' => 'module_state_message.module_restriction.disable',
                    ];
                }
            } elseif ($item['requiredBy']) {
                $actions['switch'] = false;
                $actions['remove'] = false;
                $messages[]        = [
                    'type'    => 'warning',
                    'message' => 'module_state_message.module_restriction.requiredBy',
                    'params'  => AlertType::prepareParams($this->prepareModulesList($item['requiredBy'])),
                ];
            }

            if ($item['type'] === 'skin') {
                $actions['manageLayout'] = true;
            }

        } elseif ($item['installed']) {
            $actions['install'] = false;

            if ($this->getMajorVersion($item['installedVersion']) !== $this->coreMajorVersion) {
                $actions['switch'] = false;

                if ($this->getMajorVersion($item['version']) !== $this->coreMajorVersion) {
                    if ($this->isUpgradeRequested($item)) {
                        $messages[] = [
                            'type'    => 'warning',
                            'message' => 'module_state_message.core_restriction.upgrade_request.requested',
                        ];
                    } else {
                        $actions['upgradeRequest'] = !$item['custom'];
                        $messages[]                = [
                            'type'    => 'warning',
                            'message' => 'module_state_message.core_restriction.upgrade_request',
                        ];
                    }
                }
            } elseif ($item['minorRequiredCoreVersion'] > $this->coreMinorVersion) {
                $actions['switch'] = false;
                $messages[]        = [
                    'type'    => 'warning',
                    'message' => 'module_state_message.core_restriction.enable',
                    'params'  => AlertType::prepareParams([
                        $this->coreMajorVersion . '.' . $item['minorRequiredCoreVersion'] . '.0',
                    ]),
                ];
            }

            if ($item['incompatibleWith']) {
                $processedIncompatibleList = array_filter(
                    $item['incompatibleWith'],
                    function ($incompatibleModuleId) {
                        /** @var Module $incompatibleModule */
                        $incompatibleModule = $this->modulesDataSource->findOne($incompatibleModuleId);

                        return !$incompatibleModule || $incompatibleModule->isSystem === false;
                    }
                );

                if ($processedIncompatibleList) {
                    $actions['switch'] = false;
                    $messages[]        = [
                        'type'    => 'warning',
                        'message' => 'module_state_message.module_restriction.incompatibleWith',
                        'params'  => AlertType::prepareParams($this->prepareModulesList($item['incompatibleWith'])),
                    ];
                }
            }

            if ($item['dependsOn']) {
                $messages[] = [
                    'type'    => 'warning',
                    'message' => 'module_state_message.module_restriction.dependsOn',
                    'params'  => AlertType::prepareParams($this->prepareModulesList($item['dependsOn'])),
                ];
            }

            if ($item['type'] === 'skin') {
                $actions['manageLayout'] = true;
            }
        } else {
            $actions['switch']  = false;
            $actions['install'] = true;

            $isPendingLicense = isset($license['keyType'])
                ? $license['keyType'] === LicenseDataSource::KEY_TYPE_PENDING
                : false;
            $isEmptyLicense   = empty($license) || $isPendingLicense;

            if ($isPendingLicense) {
                $messages[] = [
                    'type'    => 'warning',
                    'message' => 'module_state_message.license_pending',
                ];
            }

            if ($isEmptyLicense && $item['price'] > 0) {
                $actions['purchase'] = true;
            }

            if ($item['xcnPlan'] === -1) {
                $actions['install']  = false;
                $actions['purchase'] = false;
                $messages[]          = [
                    'type'    => 'warning',
                    'message' => $this->isCloud
                        ? 'module_state_message.edition.unavailable_in_all_cloud_editions'
                        : 'module_state_message.edition.unavailable_in_all_standalone_editions',
                ];
            } elseif (version_compare($this->getMajorVersion($item['version']), $this->coreMajorVersion, '<')) {
                $actions['install']  = false;
                $actions['purchase'] = false;

                if ($this->isUpgradeRequested($item)) {
                    $messages[] = [
                        'type'    => 'warning',
                        'message' => 'module_state_message.core_restriction.upgrade_request.requested',
                    ];
                } else {
                    $actions['upgradeRequest'] = !$item['custom'];
                    $messages[]                = [
                        'type'    => 'warning',
                        'message' => 'module_state_message.core_restriction.upgrade_request',
                    ];
                }
            } elseif (version_compare($this->getMajorVersion($item['version']), $this->coreMajorVersion, '>')) {
                $actions['install']  = false;
                $actions['purchase'] = false;

                if ($this->isUpgradeRequested($item)) {
                    $messages[] = [
                        'type'    => 'warning',
                        'message' => 'module_state_message.core_restriction.install.requested',
                        'params'  => AlertType::prepareParams([
                            $this->coreMajorVersion . '.0.0',
                        ]),
                    ];
                } else {
                    $actions['upgradeRequest'] = !$item['custom'];
                    $messages[]                = [
                        'type'    => 'warning',
                        'message' => 'module_state_message.core_restriction.install',
                        'params'  => AlertType::prepareParams([
                            $this->coreMajorVersion . '.0.0',
                        ]),
                    ];
                }
            } elseif ($isEmptyLicense && ($item['price'] > 0 || !$this->checkEdition($item))) { // todo: check license
                $actions['install'] = false;

                if (!$this->checkEdition($item)) {
                    $editions = array_map(function ($edition) {
                        [$id, $name] = explode('_', $edition);

                        $url = $this->isCloud
                            ? $this->marketplaceShop->getBuyNowURL($id, $this->urlBuilder->buildServiceMainUrl('afterPurchase'))
                            : $this->marketplaceShop->getPurchaseURL($id);

                        return [$url, $name];
                    }, $item['editions']);

                    $messages[] = [
                        'type'    => 'warning',
                        'message' => 'module_state_message.edition.unavailable',
                        'params'  => AlertType::prepareParams($editions),
                    ];
                }
            } elseif ($item['installedVersion']) {
                $actions['install'] = false;

            } elseif ($item['incompatibleWith']) {
                $actions['install'] = false;
                $messages[]         = [
                    'type'    => 'warning',
                    'message' => 'module_state_message.module_restriction.incompatibleWith',
                    'params'  => AlertType::prepareParams($this->prepareModulesList($item['incompatibleWith'])),
                ];
            }
        }

        if (
            $item['enabled'] &&
            !$item['scenarioState']['enabled'] &&
            $item['scenarioState']['installed'] &&
            $item['dependsOn']
        ) {
            $messages[] = [
                'type'    => 'warning',
                'message' => 'module_state_message.module_restriction.dependsOn',
                'params'  => AlertType::prepareParams($this->prepareModulesList($item['dependsOn'])),
            ];
        }

        if ($item->installed && $item->version) {
            if ($this->getMajorVersion($item->installedVersion) === $this->coreMajorVersion) {
                if ($this->getMajorVersion($item->version) === $this->coreMajorVersion) {
                    if ($this->displayUpdateNotification) {
                        $updateType = $this->getMinorVersion($item->version) > $this->getMinorVersion($item->installedVersion)
                            ? 'minor'
                            : 'build';

                        $messages[] = [
                            'type'    => 'success',
                            'message' => 'module_state_message.update_available',
                            'params'  => AlertType::prepareParams([
                                'version' => $item->version,
                                'type'    => $updateType,
                            ]),
                        ];
                    }
                } elseif (version_compare($this->getMajorVersion($item->version), $this->coreMajorVersion, '>')) {
                    $actions['switch'] = false;
                    $messages[]        = [
                        'type'    => 'warning',
                        'message' => 'The upgrade of the module is not released for your X-Cart version',
                    ];
                }
            } elseif (Module::isPreviuosMajorVersion($item->installedVersion, $this->coreMajorVersion)) {
                if ($this->getMajorVersion($item->version) === $this->coreMajorVersion) {
                    $messages[] = [
                        'type'    => 'warning',
                        'message' => 'module_state_message.postponed_upgrade_available',
                        'params'  => AlertType::prepareParams([
                            'version'  => $item->version,
                            'moduleId' => $item->id,
                        ]),
                    ];
                }
            }
        }

        $actions['settings'] = $item['enabled'] && $item['showSettingsForm'];

        if ($license
            && !empty($license['keyData']['expDate'])
            && $license['keyData']['expDate'] < time()
            && $item['installed']
            && $this->getMajorVersion($item['version']) === $this->coreMajorVersion
        ) {
            $messages[] = [
                'type'    => 'warning',
                'message' => 'module_state_message.license_expired',
                'params'  => AlertType::prepareParams([
                    'renewUrl' => $this->marketplaceShop->getRenewalURL($license['keyData']['prolongKey'], $license['keyValue'], $this->urlBuilder->buildServiceMainUrl('afterPurchase')),
                ]),
            ];
        }

        $item->actions  = $actions;
        $item->messages = $messages;

        return $item;
    }

    /**
     * @param $version
     *
     * @return string
     */
    private function getMajorVersion($version): string
    {
        [$system, $major, ,] = Module::explodeVersion($version);

        return $system . '.' . $major;
    }

    /**
     * @param $version
     *
     * @return int
     */
    private function getMinorVersion($version): int
    {
        [, , $minor,] = Module::explodeVersion($version);

        return (int) $minor;
    }

    /**
     * @param Module $module
     *
     * @return array|null
     */
    private function getLicense($module): ?array
    {
        if (!array_key_exists($module->id, $this->moduleLicense)) {
            $this->moduleLicense[$module->id] = null;
            foreach ($this->licenses as $keyInfo) {
                if ($keyInfo['name'] === $module->name && $keyInfo['author'] === $module->author) {
                    $this->moduleLicense[$module->id] = $keyInfo;
                    break;
                }
            }
        }

        return $this->moduleLicense[$module->id];
    }

    /**
     * @param array $modules
     *
     * @return array
     */
    private function prepareModulesList($modules): array
    {
        return array_map(function ($moduleId) {
            /** @var Module $module */
            $module = $this->modulesDataSource->findOne($moduleId);

            if (!$module) {
                [$authorName, $moduleName] = Module::explodeModuleId($moduleId);
            }

            return [
                'id'         => $moduleId,
                'authorName' => $module ? $module->authorName : $authorName,
                'moduleName' => $module ? $module->moduleName : $moduleName,
            ];
        }, $modules);
    }

    /**
     * @param Module $module
     *
     * @return bool
     */
    private function checkEdition($module): bool
    {
        $moduleEditions = $module->editions;
        $coreEdition    = $this->coreLicense
            ? ($this->coreLicense['keyData']['xbProductId'] . '_' . $this->coreLicense['keyData']['editionName'])
            : '';

        if ($moduleEditions) {
            return in_array($coreEdition, $moduleEditions, true);
        }

        return true;
    }

    /**
     * @param Module $module
     *
     * @return bool
     */
    private function isUpgradeRequested(Module $module): bool
    {
        $upgradeForRequest = $this->requestForUpgrade[$module->id] ?? 0;

        return $upgradeForRequest > $this->time;
    }
}
