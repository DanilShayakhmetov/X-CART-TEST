<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch;

use Exception;
use Includes\Utils\URLManager;
use XLite;
use XLite\Core\Config;
use XLite\Module\QSL\CloudSearch\Core\ServiceApiClient;

/**
 * CloudSearch & CloudFilters module
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Check if CloudSearch is configured
     *
     * @return boolean
     */
    public static function isConfigured()
    {
        $apiClient = new ServiceApiClient();

        $apiKey    = $apiClient->getApiKey();
        $secretKey = $apiClient->getSecretKey();

        return !empty($apiKey) && !empty($secretKey);
    }

    /**
     * Check if CloudFilters is enabled
     *
     * @return boolean
     */
    public static function isCloudFiltersEnabled()
    {
        return in_array('cloudFilters', self::getPlanFeatures())
            && (bool)Config::getInstance()->QSL->CloudSearch->isCloudFiltersEnabled;
    }

    /**
     * Check if realtime indexing is enabled
     *
     * @return boolean
     */
    public static function isRealtimeIndexingEnabled()
    {
        try {
            return in_array('realtimeIndexing', self::getPlanFeatures());
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if admin search is enabled
     *
     * @return boolean
     */
    public static function isAdminSearchEnabled()
    {
        return in_array('adminSearch', self::getPlanFeatures())
            && Config::getInstance()->QSL->CloudSearch->isAdminSearchEnabled;
    }

    /**
     * Get plan features
     *
     * @return array
     */
    public static function getPlanFeatures()
    {
        $planFeatures = Config::getInstance()->QSL->CloudSearch->planFeatures;

        $planFeatures = !empty($planFeatures) ? json_decode($planFeatures, true) : [];

        return $planFeatures ?: [];
    }

    /**
     * Check if store is set up in multi-domain mode.
     * In multi-domain mode only the main domain will be registered in CS and all links will be indexed
     * as absolute URLs without host name so that every domain can use them properly.
     *
     * @return bool
     */
    public static function isMultiDomain()
    {
        $domains = array_filter(URLManager::getShopDomains());

        return count($domains) > 1;
    }

    public static function isXCCloud()
    {
        return XLite::getInstance()->getOptions(['service', 'is_cloud']);
    }

    public static function getXCCloudHost()
    {
        return XLite::getInstance()->getOptions(['host_details', 'admin_host']);
    }
}
