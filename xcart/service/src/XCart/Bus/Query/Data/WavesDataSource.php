<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use XCart\Marketplace\Request\Waves;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class WavesDataSource extends AMarketplaceDataSetSource
{
    /**
     * @return string
     */
    protected function getRequest(): string
    {
        return Waves::class;
    }
}
