<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use Iterator;
use Silex\Application;
use XCart\Bus\Core\Annotations\DataSourceFilter;
use XCart\Bus\Editions\Core\UninstallAvailDecider;
use XCart\Bus\Helper\UrlBuilder;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Query\Data\Filter\AFilterGenerator;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Query\Data\LicenseDataSource;
use XCart\Bus\Query\Data\MarketplaceShopAdapter;
use XCart\Bus\Query\Data\ModulesDataSource;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @DataSourceFilter(name="actions")
 * @Service\Service()
 */
class ActionsGenerator extends AFilterGenerator
{
    /**
     * @var InstalledModulesDataSource
     */
    private $installedModulesDataSource;

    /**
     * @var ModulesDataSource
     */
    private $modulesDataSource;

    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * @var LicenseDataSource
     */
    private $licenseDataSource;

    /**
     * @var UninstallAvailDecider
     */
    private $uninstallAvailDecider;

    /**
     * @var MarketplaceShopAdapter
     */
    private $marketplaceShopAdapter;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var bool
     */
    private $developerMode;

    /**
     * @var bool
     */
    private $displayUpdateNotification;

    /**
     * @var bool
     */
    private $isCloud;

    /**
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param ModulesDataSource          $modulesDataSource
     * @param CoreConfigDataSource       $coreConfigDataSource
     * @param LicenseDataSource          $licenseDataSource
     * @param UninstallAvailDecider      $uninstallAvailDecider
     * @param MarketplaceShopAdapter     $marketplaceShopAdapter
     * @param UrlBuilder                 $urlBuilder
     * @param Application                $app
     */
    public function __construct(
        InstalledModulesDataSource $installedModulesDataSource,
        ModulesDataSource $modulesDataSource,
        CoreConfigDataSource $coreConfigDataSource,
        LicenseDataSource $licenseDataSource,
        UninstallAvailDecider $uninstallAvailDecider,
        MarketplaceShopAdapter $marketplaceShopAdapter,
        UrlBuilder $urlBuilder,
        Application $app
    ) {
        $this->installedModulesDataSource = $installedModulesDataSource;
        $this->modulesDataSource          = $modulesDataSource;
        $this->coreConfigDataSource       = $coreConfigDataSource;
        $this->licenseDataSource          = $licenseDataSource;
        $this->uninstallAvailDecider      = $uninstallAvailDecider;
        $this->marketplaceShopAdapter     = $marketplaceShopAdapter;
        $this->urlBuilder                 = $urlBuilder;
        $this->developerMode              = $app['config']['developer_mode'] ?? false;
        $this->displayUpdateNotification  = $app['xc_config']['service']['display_update_notification'] ?? false;
        $this->isCloud                    = $app['xc_config']['service']['is_cloud'] ?? false;
    }

    /**
     * @param Iterator $iterator
     * @param string   $field
     * @param mixed    $data
     *
     * @return Actions
     */
    public function __invoke(Iterator $iterator, $field, $data)
    {
        return new Actions(
            $iterator,
            $field,
            $data,
            $this->installedModulesDataSource,
            $this->modulesDataSource,
            $this->coreConfigDataSource,
            $this->licenseDataSource,
            $this->uninstallAvailDecider,
            $this->marketplaceShopAdapter->get(),
            $this->urlBuilder,
            $this->developerMode,
            $this->displayUpdateNotification,
            $this->isCloud
        );
    }
}
