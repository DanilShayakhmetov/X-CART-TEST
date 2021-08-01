<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use XCart\Bus\Client\MarketplaceClient;
use XCart\Bus\Query\Data\Buffer\DataSet;
use XCart\Marketplace\Constant;
use XCart\Marketplace\Request\Notifications;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class NotificationsDataSource extends AMarketplaceDataSetSource
{
    /**
     * @return string
     */
    protected function getRequest(): string
    {
        return Notifications::class;
    }

    /**
     * @return int
     */
    protected function getLifetime(): int
    {
        return 0;
    }

    /**
     * @return bool
     */
    protected function isExpired(): bool
    {
        return true;
    }
}
