<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon;

use XLite\Core\Cache\ExecuteCached;

/**
 * PayWithAmazon module main class
 */
abstract class Main extends \XLite\Module\AModule
{
    const PLATFORM_ID = 'A1PQFSSKP8TT2U';

    public static function log($message)
    {
        \XLite\Logger::logCustom('amazon_pa', $message);
    }

    /**
     * @return Object|\XLite\Model\Payment\Method
     */
    public static function getMethod()
    {
        return ExecuteCached::executeCachedRuntime(function () {
            return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                ->findOneBy(['service_name' => 'PayWithAmazon']);
        }, [__CLASS__, __FUNCTION__]);
    }

    /**
     * @return \XLite\Module\Amazon\PayWithAmazon\Model\Payment\Processor\PayWithAmazon
     */
    public static function getProcessor()
    {
        return static::getMethod()->getProcessor();
    }

    /**
     * @return bool
     */
    public static function isSCAFlowNeed()
    {
        return ExecuteCached::executeCachedRuntime(function () {
            $method = static::getMethod();

            return in_array($method->getSetting('region'), ['EUR', 'GBP']);
        }, [__CLASS__, __FUNCTION__]);
    }

    /**
     * @return \PayWithAmazon\Client
     */
    public static function getClient()
    {
        return ExecuteCached::executeCachedRuntime(function () {
            $method    = static::getMethod();
            $processor = static::getProcessor();
            //$config       = \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon;
            $clientConfig = [
                'merchant_id'   => $method->getSetting('merchant_id'),
                'access_key'    => $method->getSetting('access_key'),
                'secret_key'    => $method->getSetting('secret_key'),
                'client_id'     => $method->getSetting('client_id'),
                'region'        => \XLite\Module\Amazon\PayWithAmazon\View\FormField\Select\Region::getRegionByCurrency($method->getSetting('region')),
                'currency_code' => $method->getSetting('region'),
                'sandbox'       => $processor->isTestMode($method),
            ];

            static::includeClient();

            return new \AmazonPay\Client($clientConfig);
        }, [__CLASS__, __FUNCTION__]);
    }

    public static function includeClient()
    {
        require_once LC_DIR_MODULES . 'Amazon' . LC_DS . 'PayWithAmazon' . LC_DS . 'lib' . LC_DS . 'vendor' . LC_DS . 'amzn' . LC_DS . 'amazon-pay-sdk-php' . LC_DS . 'AmazonPay' . LC_DS . 'Client.php';
    }

    public static function includeIPNHandler()
    {
        require_once LC_DIR_MODULES . 'Amazon' . LC_DS . 'PayWithAmazon' . LC_DS . 'lib' . LC_DS . 'vendor' . LC_DS . 'amzn' . LC_DS . 'amazon-pay-sdk-php' . LC_DS . 'AmazonPay' . LC_DS . 'IpnHandler.php';
    }
}
