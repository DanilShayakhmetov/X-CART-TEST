<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use Iterator;
use XCart\Bus\Core\Annotations\DataSourceFilter;
use XCart\Bus\Helper\UrlBuilder;
use XCart\Bus\Query\Data\Filter\AFilterGenerator;
use XCart\Bus\Query\Data\MarketplaceShopAdapter;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @DataSourceFilter(name="purchaseUrl")
 * @Service\Service()
 */
class PurchaseUrlGenerator extends AFilterGenerator
{
    /**
     * @var MarketplaceShopAdapter
     */
    private $marketplaceShopAdapter;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @param MarketplaceShopAdapter $marketplaceShopAdapter
     * @param UrlBuilder             $urlBuilder
     */
    public function __construct(
        MarketplaceShopAdapter $marketplaceShopAdapter,
        UrlBuilder $urlBuilder
    ) {
        $this->marketplaceShopAdapter = $marketplaceShopAdapter;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param Iterator $iterator
     * @param string   $field
     * @param mixed    $data
     *
     * @return PurchaseUrl
     */
    public function __invoke(Iterator $iterator, $field, $data)
    {
        return new PurchaseUrl(
            $iterator,
            $field,
            $data,
            $this->marketplaceShopAdapter,
            $this->urlBuilder
        );
    }
}
