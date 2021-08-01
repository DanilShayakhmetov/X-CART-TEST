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
class PaymentMethodsDataSource extends AMarketplaceDataSource
{
    /**
     * @param string $countryCode
     */
    public function getForCountry(string $countryCode = '')
    {
        $data = $this->getData();

        if (!empty($data[$countryCode])) {
            return $data[$countryCode];
        }

        $data[$countryCode] = $this->client->getAllPaymentMethods($countryCode);
        $this->saveAll($data);

        return $data[$countryCode];
    }

    /**
     * @return mixed
     */
    protected function doRequest()
    {
        return $this->client->getAllPaymentMethods();
    }
}
