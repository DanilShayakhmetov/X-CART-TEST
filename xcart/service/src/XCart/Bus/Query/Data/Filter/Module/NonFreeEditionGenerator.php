<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use Iterator;
use XCart\Bus\Client\LicenseClient;
use XCart\Bus\Core\Annotations\DataSourceFilter;
use XCart\Bus\Query\Data\Filter\AFilterGenerator;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @DataSourceFilter(name="nonFreeEdition")
 * @Service\Service()
 */
class NonFreeEditionGenerator extends AFilterGenerator
{
    /**
     * @var LicenseClient
     */
    private $licenseClient;

    /**
     * @param LicenseClient $licenseClient
     */
    public function __construct(LicenseClient $licenseClient)
    {
        $this->licenseClient = $licenseClient;
    }

    /**
     * @param Iterator $iterator
     * @param string   $field
     * @param mixed    $data
     *
     * @return NonFreeEdition
     */
    public function __invoke(Iterator $iterator, $field, $data)
    {
        return new NonFreeEdition($iterator, $field, $data, $this->licenseClient);
    }
}
