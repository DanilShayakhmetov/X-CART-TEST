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
 * @DataSourceFilter(name="onlyAvailable")
 * @Service\Service()
 */
class OnlyAvailableGenerator extends AFilterGenerator
{
    /**
     * @var string
     */
    private $coreEdition;

    /**
     * @var string
     */
    private $coreVersion;

    /**
     * @param LicenseDataSource          $licenseDataSource
     * @param InstalledModulesDataSource $installedModulesDataSource
     */
    public function __construct(
        LicenseDataSource $licenseDataSource,
        InstalledModulesDataSource $installedModulesDataSource
    ) {
        $coreLicense = $licenseDataSource->findBy([
            'author' => 'CDev',
            'name'   => 'Core',
        ]);

        $core             = $installedModulesDataSource->find('CDev-Core');
        $installationDate = $core ? $core['installedDate'] : 0;
        $endTime          = $installationDate + 86400 * 30;

        if ($coreLicense) {
            $this->coreEdition = $coreLicense['keyData']['editionName'] ?? 'Trial';
        } elseif ($endTime <= time()) {
            $this->coreEdition = 'Trial';
        }

        [$system, $major, ,] = Module::explodeVersion($core->version);
        $this->coreVersion = $system . '.' . $major;
    }

    /**
     * @param Iterator $iterator
     * @param string   $field
     * @param mixed    $data
     *
     * @return Licensed
     */
    public function __invoke(Iterator $iterator, $field, $data)
    {
        return new OnlyAvailable(
            $iterator,
            $field,
            $data,
            $this->coreEdition,
            $this->coreVersion
        );
    }
}
