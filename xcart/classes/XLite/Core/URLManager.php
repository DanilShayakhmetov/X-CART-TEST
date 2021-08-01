<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Abstract URL manager
 */
abstract class URLManager extends \Includes\Utils\URLManager
{
    /**
     * Results cache for https check requests
     *
     * @var array
     */
    protected static $requestCache = array();

    /**
     * Check if store is accessible via secure protocol
     *
     * @param string  $url      URL to validate
     * @param boolean $checkSSL Check validity of SSL certificate OPTIONAL
     *
     * @return boolean
     */
    public static function isSecureURLAccessible($url, $checkSSL = false)
    {
        $key = sprintf('%d-%s', intval($checkSSL), $url);

        if (!isset(self::$requestCache[$key])) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $checkSSL);

            curl_exec($ch);

            $info = curl_getinfo($ch);

            curl_close($ch);

            self::$requestCache[$key] = in_array($info['http_code'], array(200, 301, 302));
        }

        return self::$requestCache[$key];
    }

    /**
     * Return true if specified URL belongs to the allowed domain name
     *
     * @param string  $url    URL
     * @param boolean $strict URL can be relative or just with params (if strict = false) OPTIONAL
     *
     * @return boolean
     */
    public static function isValidDomain($url, $strict = true)
    {
        $result = false;

        $port = parse_url($url, PHP_URL_PORT);
        $domain = parse_url($url, PHP_URL_HOST) . ($port ? (':' . $port) : '');

        $path = parse_url($url, PHP_URL_PATH);

        // Checks if there is a redirect hack in returnURL. See bug #BUG-3904
        $hack_attempt = (strpos($path, '\\') !== false && strpos($path, '\\') < 2);

        if ($domain) {
            $result = in_array($domain, static::getAllowedDomains());

        } elseif (!$strict && !$hack_attempt) {
            $result = true;
        }

        return $result;
    }

    /**
     * Get allowed domains 
     * 
     * @return array
     */
    public static function getAllowedDomains()
    {
        return array_unique(array_merge(static::getShopDomains(), static::getTrustedDomains()));
    }

    /**
     * Get array of trusted domains
     *
     * @return array
     */
    public static function getTrustedDomains()
    {
        $result = array();

        $trustedURLs = \Includes\Utils\ConfigParser::getOptions(array('other', 'trusted_domains'));

        if (!empty($trustedURLs)) {
            $result = array_map('trim', explode(',', $trustedURLs));
        }

        return $result;
    }

    /**
     * @param $link
     * @param $paramsToAppend
     *
     * @return string
     */
    public static function appendParamsToUrl($link, $paramsToAppend) {
        $fragment = parse_url($link, PHP_URL_FRAGMENT);
        $delimiter = (strpos($link, '?') === false ? '?' : '&');

        $base = $link;
        $tail = '';
        if ($fragment) {
            $base = str_replace('#' . $fragment, '', $link);
            $tail = '#' . $fragment;
        }

        return $base . $delimiter . http_build_query($paramsToAppend, null, '&') . $tail;
    }

    /**
     * Get options
     *
     * @param mixed $option Option
     *
     * @return mixed
     */
    protected static function getOptions($option)
    {
        return \XLite::getInstance()->getOptions($option);
    }

}
