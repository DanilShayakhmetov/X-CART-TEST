<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed;

use XLite\Core\Config;
use XLite\Module\XC\GoogleFeed\Logic\Feed\Generator;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return \XLite\Core\Converter::buildURL('google_feed');
    }

    /**
     * Returns public available google feed url
     */
    public static function getGoogleFeedUrl()
    {
        if (!Generator::getInstance() || !Generator::getInstance()->isGenerated()) {
            return null;
        }

        if (!static::getFeedKey()) {
            static::generateFeedKey();
        }

        return \XLite\Core\Converter::buildFullURL('google_feed', '', [
            'key' => static::getFeedKey(),
        ], \XLite::CART_SELF);
    }

    /**
     * @return bool
     */
    public static function shouldExportDuplicates()
    {
        return Config::getInstance()->XC->GoogleFeed->duplicate_policy === 'export_as_separate';
    }

    /**
     * Returns HTTPS-ready absolute url without xid parameter
     *
     * @param string $url    Inner URL part
     * @param array  $params Query params
     *
     * @return string
     */
    public static function getShopURL($url, $params = [])
    {
        return \XLite\Core\URLManager::getShopURL(
            $url,
            \XLite\Core\Config::getInstance()->Security->customer_security,
            $params,
            null,
            false
        );
    }

    /**
     * Return google feed key
     *
     * @return mixed
     */
    protected static function getFeedKey()
    {
        if (\XLite\Core\Config::getInstance()->XC->GoogleFeed) {
            return \XLite\Core\Config::getInstance()->XC->GoogleFeed->feed_key;
        }

        return null;
    }

    /**
     * Generate & set product feed key
     */
    protected static function generateFeedKey()
    {
        $key = \Includes\Utils\Operator::generateHash(32);

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'XC\GoogleFeed',
            'name'     => 'feed_key',
            'value'    => $key,
        ]);

        \XLite\Core\Config::updateInstance();
    }
}
