<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use Silex\Application;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\Bus\Helper\UrlBuilder;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Query\Data\EditionsDataSource;
use XCart\Bus\Query\Data\MarketplaceShopAdapter;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class EditionsResolver
{
    /**
     * @var EditionsDataSource
     */
    private $editionsDataSource;

    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * @var MarketplaceShopAdapter
     */
    private $marketplaceShopAdapter;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var string
     */
    private $email;

    /**
     * @param Application            $app
     * @param EditionsDataSource     $editionsDataSource
     * @param CoreConfigDataSource   $coreConfigDataSource
     * @param MarketplaceShopAdapter $marketplaceShopAdapter
     * @param UrlBuilder             $urlBuilder
     *
     * @return EditionsResolver
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        EditionsDataSource $editionsDataSource,
        CoreConfigDataSource $coreConfigDataSource,
        MarketplaceShopAdapter $marketplaceShopAdapter,
        UrlBuilder $urlBuilder
    ) {
        return new self(
            $editionsDataSource,
            $coreConfigDataSource,
            $marketplaceShopAdapter,
            $urlBuilder,
            $app['xc_config']['service']['cloud_account_email'] ?? $app['config']['email']
        );
    }

    /**
     * @param EditionsDataSource     $editionsDataSource
     * @param CoreConfigDataSource   $coreConfigDataSource
     * @param MarketplaceShopAdapter $marketplaceShopAdapter
     * @param UrlBuilder             $urlBuilder
     * @param string                 $email
     */
    public function __construct(
        EditionsDataSource $editionsDataSource,
        CoreConfigDataSource $coreConfigDataSource,
        MarketplaceShopAdapter $marketplaceShopAdapter,
        UrlBuilder $urlBuilder,
        $email
    ) {
        $this->editionsDataSource     = $editionsDataSource;
        $this->coreConfigDataSource   = $coreConfigDataSource;
        $this->marketplaceShopAdapter = $marketplaceShopAdapter;
        $this->urlBuilder             = $urlBuilder;
        $this->email                  = $email;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return Deferred
     *
     * @Resolver()
     */
    public function getList($value, $args, $context, ResolveInfo $info)
    {
        $this->editionsDataSource->loadDeferred();

        return new Deferred(function () {
            $marketplaceShop = $this->marketplaceShopAdapter->get();

            $editions = array_map(function ($edition) use ($marketplaceShop) {
                $edition['purchase_url'] = $marketplaceShop->getBuyNowURL(
                    $edition['xb_product_id'],
                    $this->urlBuilder->buildServiceMainUrl('afterCloudPurchase'),
                    [
                        'email'               => $this->email,
                        'invoice_description' => "Trial ID:{$this->email}",
                    ]
                );

                return $edition;
            }, $this->editionsDataSource->getAll());

            return $editions;
        });
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return bool
     *
     * @Resolver()
     */
    public function isCancelledSubscription($value, $args, $context, ResolveInfo $info): bool
    {
        return $this->coreConfigDataSource->isCancelledSubscription ?? false;
    }
}
