<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use Silex\Application;
use XCart\Bus\Helper\UrlBuilder;
use XCart\SilexAnnotations\Annotations\Service;
use XCart\MarketplaceShop as MarketplaceShopOriginal;

/**
 * @Service\Service()
 */
class MarketplaceShopAdapter
{
    /**
     * @var MarketplaceShopOriginal
     */
    private $marketplaceShop;

    /**
     * MarketplaceShop constructor.
     *
     * @param MarketplaceShopOriginal $marketplaceShop
     */
    public function __construct(MarketplaceShopOriginal $marketplaceShop)
    {
        $this->marketplaceShop = $marketplaceShop;
    }

    /**
     * @param Application       $app
     * @param UrlBuilder        $urlBuilder
     * @param LicenseDataSource $licenseDataSource
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        UrlBuilder $urlBuilder,
        LicenseDataSource $licenseDataSource
    ) {
        $licenseKey = $licenseDataSource->findBy([
            'author' => 'CDev',
            'name' => 'Core'
        ]);

        $licenseKeyValue = $licenseKey ? $licenseKey['keyValue'] : '';

        return new static(
            MarketplaceShopOriginal::build(
                $urlBuilder->buildAdminUrl(),
                $app['x_cart.bus.token_data']['admin_login'] ?? '',
                md5($licenseKeyValue),
                $app['config']['affiliate_id'],
                'service.php',
                $app['config']['installation_lng'],
                $app['config']['marketplace.xb_host']
            )
        );
    }

    /**
     * @return MarketplaceShopOriginal
     */
    public function get()
    {
        return $this->marketplaceShop;
    }
}
