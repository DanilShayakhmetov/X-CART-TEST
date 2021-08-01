<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use XCart\Bus\Client\MarketplaceClient;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\Bus\Query\Context;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Query\Data\LicenseDataSource;
use XCart\Bus\Query\Data\MarketplaceModulesDataSource;
use XCart\Bus\Query\Data\WavesDataSource;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class WavesResolver
{
    /**
     * @var WavesDataSource
     */
    private $wavesDataSource;

    /**
     * @var LicenseDataSource
     */
    private $licenseDataSource;

    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * @var MarketplaceModulesDataSource
     */
    private $marketplaceModulesDataSource;

    /**
     * @var MarketplaceClient
     */
    private $marketplaceClient;

    /**
     * @param WavesDataSource              $wavesDataSource
     * @param LicenseDataSource            $licenseDataSource
     * @param CoreConfigDataSource         $coreConfigDataSource
     * @param MarketplaceModulesDataSource $marketplaceModulesDataSource
     * @param MarketplaceClient            $marketplaceClient
     */
    public function __construct(
        WavesDataSource $wavesDataSource,
        LicenseDataSource $licenseDataSource,
        CoreConfigDataSource $coreConfigDataSource,
        MarketplaceModulesDataSource $marketplaceModulesDataSource,
        MarketplaceClient $marketplaceClient
    ) {
        $this->wavesDataSource              = $wavesDataSource;
        $this->licenseDataSource            = $licenseDataSource;
        $this->coreConfigDataSource         = $coreConfigDataSource;
        $this->marketplaceModulesDataSource = $marketplaceModulesDataSource;
        $this->marketplaceClient            = $marketplaceClient;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param             $value
     * @param             $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return Deferred|array
     *
     * @Resolver()
     */
    public function getList($value, $args, Context $context, ResolveInfo $info)
    {
        if ($this->licenseDataSource->getAll()) {
            $this->wavesDataSource->loadDeferred();

            return new Deferred(function () {
                return $this->wavesDataSource->getAll();
            });
        }

        return [];
    }

    /**
     * @param mixed       $value
     * @param array       $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return array
     *
     * @Resolver()
     */
    public function changeWave($value, $args, Context $context, ResolveInfo $info)
    {
        if (!($context->mode === Context::ACCESS_MODE_FULL)) {
            return [];
        }

        $wave = $args['wave'] ?? '127';

        $this->coreConfigDataSource->wave = $wave;

        $keys = [];

        $licenses = $this->licenseDataSource->getAll();
        foreach ($licenses as $license) {
            $keys[] = $license['keyValue'];
        }

        $this->marketplaceClient->setKeyWave($keys, $wave);

        $this->coreConfigDataSource->dataDate = time();
        $this->marketplaceModulesDataSource->clear();

        return ['id' => $wave];
    }
}
