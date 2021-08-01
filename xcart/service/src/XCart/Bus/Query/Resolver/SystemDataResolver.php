<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use Silex\Application;
use XCart\Bus\Client\MarketplaceClient;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\Bus\Domain\Module;
 use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Query\Data\MarketplaceShopAdapter;
use XCart\Bus\Query\Data\ModulesDataSource;
use XCart\Bus\System\DBInfo;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class SystemDataResolver
{
    /**
     * @var InstalledModulesDataSource
     */
    private $installedModulesDataSource;

    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * @var ModulesDataSource
     */
    private $modulesDataSource;

    /**
     * @var MarketplaceClient
     */
    private $marketplaceClient;

    /**
     * @var MarketplaceShopAdapter
     */
    private $marketplaceShopAdapter;

    /**
     * @var DBInfo
     */
    private $dbInfo;

    /**
     * @var bool
     */
    private $pharIsInstalled;

    /**
     * @var string
     */
    private $email;

    /**
     * @var bool
     */
    private $demoMode;

    /**
     * @var bool
     */
    private $isCloud;

    /**
     * @var bool
     */
    private $displayUploadAddon;

    private $displayUpdateNotification;

    /**
     * @param Application                $app
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param CoreConfigDataSource       $coreConfigDataSource
     * @param ModulesDataSource          $modulesDataSource
     * @param MarketplaceClient          $marketplaceClient
     * @param MarketplaceShopAdapter     $marketplaceShopAdapter
     * @param DBInfo                     $dbInfo
     *
     * @return SystemDataResolver
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        InstalledModulesDataSource $installedModulesDataSource,
        CoreConfigDataSource $coreConfigDataSource,
        ModulesDataSource $modulesDataSource,
        MarketplaceClient $marketplaceClient,
        MarketplaceShopAdapter $marketplaceShopAdapter,
        DBInfo $dbInfo
    ) {
        return new self(
            $installedModulesDataSource,
            $coreConfigDataSource,
            $modulesDataSource,
            $marketplaceClient,
            $marketplaceShopAdapter,
            $dbInfo,
            $app['config']['phar_is_installed'],
            $app['config']['email'],
            $app['xc_config']['demo']['demo_mode'] ?? false,
            $app['xc_config']['service']['is_cloud'] ?? false,
            $app['xc_config']['service']['display_upload_addon'] ?? true,
            $app['xc_config']['service']['display_update_notification'] ?? true
        );
    }

    /**
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param CoreConfigDataSource       $coreConfigDataSource
     * @param ModulesDataSource          $modulesDataSource
     * @param MarketplaceClient          $marketplaceClient
     * @param MarketplaceShopAdapter     $marketplaceShopAdapter
     * @param DBInfo                     $dbInfo
     * @param boolean                    $pharIsInstalled
     * @param string                     $email
     * @param boolean                    $demoMode
     * @param boolean                    $isCloud
     * @param boolean                    $displayUploadAddon
     * @param                            $displayUpdateNotification
     */
    public function __construct(
        InstalledModulesDataSource $installedModulesDataSource,
        CoreConfigDataSource $coreConfigDataSource,
        ModulesDataSource $modulesDataSource,
        MarketplaceClient $marketplaceClient,
        MarketplaceShopAdapter $marketplaceShopAdapter,
        DBInfo $dbInfo,
        $pharIsInstalled,
        $email,
        $demoMode,
        $isCloud,
        $displayUploadAddon,
        $displayUpdateNotification
    ) {
        $this->installedModulesDataSource = $installedModulesDataSource;
        $this->coreConfigDataSource       = $coreConfigDataSource;
        $this->modulesDataSource          = $modulesDataSource;
        $this->marketplaceClient          = $marketplaceClient;
        $this->marketplaceShopAdapter     = $marketplaceShopAdapter;
        $this->dbInfo                     = $dbInfo;
        $this->pharIsInstalled            = $pharIsInstalled;
        $this->email                      = $email;
        $this->demoMode                   = $demoMode;
        $this->isCloud                    = $isCloud;
        $this->displayUploadAddon         = $displayUploadAddon;
        $this->displayUpdateNotification  = $displayUpdateNotification;
    }

    /**
     * @return string
     */
    public function getMysqlVersion(): string
    {
        $dbInfo = $this->coreConfigDataSource->dbInfo;

        $expiration = $dbInfo['expiration'] ?? 0;
        if ($expiration < time()) {
            $dbInfo['version']    = $this->dbInfo->getDBVersion();
            $dbInfo['expiration'] = time() + (60 * 60 * 24);
        }

        return $dbInfo['version'];
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return array
     *
     * @Resolver()
     */
    public function resolveInstallationData($value, $args, $context, ResolveInfo $info): array
    {
        $core             = $this->installedModulesDataSource->find('CDev-Core');
        $installationDate = $core ? $core['installedDate'] : 0;

        $backupMaster            = $this->installedModulesDataSource->find('QSL-Backup');
        $backupMasterIsEnabled   = $backupMaster ? $backupMaster['enabled'] : false;
        $backupMasterIsInstalled = $backupMaster ? $backupMaster['installed'] : false;

        return [
            'installationDate'        => $installationDate,
            'trialExpired'            => ($installationDate + $this->installedModulesDataSource::TRIAL_PERIOD - time()) <= 0,
            'backupMasterIsEnabled'   => $backupMasterIsEnabled,
            'backupMasterIsInstalled' => $backupMasterIsInstalled,
            'coreVersion'             => $core->version
        ];
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return Deferred
     *
     * @Resolver()
     */
    public function resolveSystemData($value, $args, $context, ResolveInfo $info): Deferred
    {
        return new Deferred(function () {
            $marketplaceLockExpiration = $this->coreConfigDataSource->find('marketplaceLockExpiration');

            return [
                'cacheDate'                 => $this->coreConfigDataSource->cacheDate ?: 0,
                'dataDate'                  => $this->coreConfigDataSource->dataDate ?: 0,
                'authLock'                  => $this->coreConfigDataSource->authLock ?: 0,
                'wave'                      => $this->coreConfigDataSource->wave,
                'shopIsClosed'              => $this->coreConfigDataSource->shopIsClosed,
                'shopKey'                   => $this->coreConfigDataSource->shopKey,
                'marketplaceLock'           => $marketplaceLockExpiration && time() < (int) $marketplaceLockExpiration,
                'purchaseUrl'               => $this->marketplaceShopAdapter->get()->getPurchaseURL(),
                'pharIsInstalled'           => $this->pharIsInstalled,
                'email'                     => $this->email,
                'demoMode'                  => $this->demoMode,
                'isCloud'                   => $this->isCloud,
                'displayUploadAddon'        => $this->displayUploadAddon,
                'displayUpdateNotification' => $this->displayUpdateNotification,
            ];
        });
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return Deferred
     *
     * @Resolver()
     */
    public function resolveStorefrontStatus($value, $args, $context, ResolveInfo $info): Deferred
    {
        return new Deferred(function () {
            return [
                'shopIsClosed' => $this->coreConfigDataSource->shopIsClosed,
                'shopKey'      => $this->coreConfigDataSource->shopKey,
            ];
        });
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return Deferred
     *
     * @Resolver()
     */
    public function resolveMarketplaceState($value, $args, $context, ResolveInfo $info): Deferred
    {
        if (!empty($args['force'])) {
            $this->marketplaceClient->getTest();
        }

        return new Deferred(function () {
            $marketplaceLockExpiration = $this->coreConfigDataSource->find('marketplaceLockExpiration');

            return [
                'marketplaceLock' => $marketplaceLockExpiration && time() < (int) $marketplaceLockExpiration,
            ];
        });
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return array
     *
     * @Resolver()
     */
    public function resolveSkinData($value, $args, $context, ResolveInfo $info): array
    {
        $skin = null;

        /** @var Module $module */
        foreach ($this->installedModulesDataSource->getAll() as $module) {
            if ($module->type === 'skin' && $module->enabled) {
                //find last
                $skin = $module;
            }
        }

        $skinData = $skin ? $this->modulesDataSource->findOne($skin->id) : null;

        return [
            'name'      => $skin ? $skin->moduleName : 'Standard',
            'marketUrl' => $skinData ? $skinData->pageUrl : null,
        ];
    }
}
