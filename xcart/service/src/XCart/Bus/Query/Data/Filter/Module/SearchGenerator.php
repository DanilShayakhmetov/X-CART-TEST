<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use Iterator;
use XCart\Bus\Client\CloudSearchProvider;
use XCart\Bus\Core\Annotations\DataSourceFilter;
use XCart\Bus\Query\Data\Filter\AFilterGenerator;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @DataSourceFilter(name="search")
 * @Service\Service()
 */
class SearchGenerator extends AFilterGenerator
{
    /**
     * @var CloudSearchProvider
     */
    protected $cloudSearchProvider;

    /**
     * @param CloudSearchProvider $cloudSearchProvider
     */
    public function __construct(CloudSearchProvider $cloudSearchProvider)
    {
        $this->cloudSearchProvider = $cloudSearchProvider;
    }

    /**
     * @param Iterator $iterator
     * @param string   $field
     * @param mixed    $data
     *
     * @return Search
     */
    public function __invoke(Iterator $iterator, $field, $data)
    {
        return new Search($iterator, $field, $data, $this->cloudSearchProvider);
    }
}
