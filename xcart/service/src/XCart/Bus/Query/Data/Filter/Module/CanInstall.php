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
use XCart\Bus\Query\Data\Filter\AFilter;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Query\Data\LicenseDataSource;
use XCart\SilexAnnotations\Annotations\Service;

class CanInstall extends AFilter
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
     * @param Iterator $iterator
     * @param string   $field
     * @param mixed    $data
     * @param string   $coreMajorVersion
     * @param          $licenses
     */
    public function __construct(
        Iterator $iterator,
        $field,
        $data,
        $coreMajorVersion,
        $licenses
    ) {
        parent::__construct($iterator, $field, $data);

        $this->coreMajorVersion  = $coreMajorVersion;
        $this->licenses          = $licenses;
    }

    /**
     * @return bool
     */
    public function accept()
    {
        /** @var Module $item */
        $item = $this->getInnerIterator()->current();

        if (!$item->version) {
            return false;
        }

        if ($item->installedVersion) {
            return false;
        }

        if ($item->incompatibleWith) {
            return false;
        }

        $majorVersion = $this->getMajorVersion($item->version);

        if (version_compare($majorVersion, $this->coreMajorVersion, '<')) {
            return false;
        }

        if (version_compare($majorVersion, $this->coreMajorVersion, '>')) {
            return false;
        }

        $license = $this->getLicense($item);
        $isEmptyLicense = empty($license)
            || (isset($license['keyType']) && $license['keyType'] === LicenseDataSource::KEY_TYPE_PENDING);

        return !($isEmptyLicense && $item->price > 0);
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
     * @param Module $module
     *
     * @return array|null
     */
    private function getLicense($module): ?array
    {
        foreach ($this->licenses as $keyInfo) {
            if ($keyInfo['name'] === $module->name && $keyInfo['author'] === $module->author) {
                return $keyInfo;
            }
        }

        return null;
    }
}
