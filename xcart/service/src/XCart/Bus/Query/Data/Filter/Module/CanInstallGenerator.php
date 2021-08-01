<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use Iterator;
use XCart\Bus\Core\Annotations\DataSourceFilter;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\Filter\AFilterGenerator;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Query\Data\LicenseDataSource;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @DataSourceFilter(name="canInstall")
 * @Service\Service()
 */
class CanInstallGenerator extends AFilterGenerator
{
    /**
     * @var string
     */
    private $coreMajorVersion;

    /**
     * @var array
     */
    private $licenses;

    /**
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param LicenseDataSource          $licenseDataSource
     */
    public function __construct(
        InstalledModulesDataSource $installedModulesDataSource,
        LicenseDataSource $licenseDataSource
    ) {
        /** @var Module $core */
        $core = $installedModulesDataSource->find('CDev-Core');

        $this->coreMajorVersion = $this->getMajorVersion($core->version);
        $this->licenses         = $licenseDataSource->getAll();
    }

    /**
     * @param Iterator $iterator
     * @param string   $field
     * @param mixed    $data
     *
     * @return CanInstall
     */
    public function __invoke(Iterator $iterator, $field, $data)
    {
        return new CanInstall(
            $iterator,
            $field,
            $data,
            $this->coreMajorVersion,
            $this->licenses
        );
    }

    /**
     * @param string $version
     *
     * @return string
     */
    private function getMajorVersion($version): string
    {
        [$system, $major, ,] = Module::explodeVersion($version);

        return $system . '.' . $major;
    }
}
