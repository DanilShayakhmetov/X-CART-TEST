<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ShippingMethodsDataSource extends AMarketplaceDataSource
{
    /**
     * @return mixed
     */
    protected function doRequest()
    {
        return $this->client->getAllShippingMethods();
    }
}
