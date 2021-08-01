<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

use XLite\Core\Auth;
use XLite\Core\Session;

/**
 * URLManager
 *
 */
abstract class URLManager extends \Includes\Utils\AUtils
{
    /**
     * URL output type codes
     */
    const URL_OUTPUT_SHORT = 'short';
    const URL_OUTPUT_FULL  = 'full';

    /**
     * @var bool https flag
     */
    protected static $isHTTPS;

    /**
     * @param      $url
     * @param null $time
     *
     * @return string
     */
    public static function addTimestampToUrl($url, $time = null)
    {
        return static::addParamToUrl(
            $url,
            't',
            $time ?: time()
        );
    }

    /**
     * @param string $url
     * @param string $paramKey
     * @param string $paramValue
     *
     * @return string
     */
    public static function addParamToUrl($url, $paramKey, $paramValue)
    {
        $query = parse_url($url, PHP_URL_QUERY);
        $delimiter = $query ? '&' : '?';

        return "{$url}{$delimiter}{$paramKey}={$paramValue}";
    }

    /**
     * Remove trailing slashes from URL
     *
     * @param string $url URL to prepare
     *
     * @return string
     */
    public static function trimTrailingSlashes($url)
    {
        return \Includes\Utils\Converter::trimTrailingChars($url, '/');
    }

    /**
     * Return full URL for the resource
     *
     * @param string  $url             URL part to add           OPTIONAL
     * @param boolean $isSecure        Use HTTP or HTTPS         OPTIONAL
     * @param array   $params          URL parameters            OPTIONAL
     * @param string  $output          URL output type           OPTIONAL
     * @param boolean $isSession       Use session ID parameter  OPTIONAL
     * @param boolean $isProtoRelative Use protocol-relative URL OPTIONAL
     *
     * @return string
     */
    public static function getShopURL(
        $url = '',
        $isSecure = null,
        array $params = [],
        $output = null,
        $isSession = null,
        $isProtoRelative = false
    ) {
        $url = trim($url);
        if (!preg_match('/^https?:\/\//Ss', $url)) {

            // We are using the protocol-relative URLs for resources
            $protocol = (true === $isSecure || ($isSecure === null && static::isHTTPS())) ? 'https' : 'http';

            if (!isset($output)) {
                $output = static::URL_OUTPUT_FULL;
            }

            $hostDetails = static::getOptions('host_details');
            $host = $hostDetails[$protocol . '_host'];
            $adminHost = $hostDetails['admin_host'] ?? null;

            if (!empty($adminHost)) {
                $host = static::getHostByLocalUrl($url);

                if (empty($url) && $adminHost !== $host && Auth::getInstance()->isAdmin()) {
                    $params[Session::ARGUMENT_NAME] = Session::getInstance()->getSecondDomainSessionId();
                }
            }

            if (!$host && !\Includes\Utils\ConfigParser::getOptions(['database_details', 'database'])) {
                $phpSelf = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
                $host = $_SERVER['HTTP_HOST'] . $phpSelf;
            }

            if ($host) {
                if (strpos($url, '/') !== 0) {
                    $url = $hostDetails['web_dir_wo_slash'] . '/' . $url;
                }

                foreach ($params as $name => $value) {
                    $url .= (false !== strpos($url, '?') ? '&' : '?') . $name . '=' . $value;
                }

                if (static::URL_OUTPUT_FULL == $output) {
                    if (strpos($url, '//') !== 0) {
                        $url = '//' . $host . $url;
                    }

                    $url = ($isProtoRelative ? '' : ($protocol . ':')) . $url;
                }
            }
        }

        return $url;
    }

    /**
     * @param string $url
     *
     * @return mixed|null
     */
    public static function getHostByLocalUrl(string $url = null)
    {
        $protocol = static::isHTTPS() ? 'https' : 'http';

        $hostDetails = static::getOptions('host_details');
        $host        = $hostDetails[$protocol . '_host'];
        $adminHost   = $hostDetails['admin_host'] ?? null;

        if (!empty($url) && (strpos($url, 'service.php') === 0 || strpos($url, \XLite::getAdminScript()) === 0)) {
            return $adminHost;
        }

        if ($adminHost === $host
            && !empty($hostDetails[$protocol . '_host_orig'])
            && (empty($url) || strpos($url, 'xid=') !== false)
        ) {
            return $hostDetails[$protocol . '_host_orig'];
        }

        return $host;
    }

    /**
     * Return protocol-relative URL for the resource
     *
     * @param string  $url    URL part to add OPTIONAL
     * @param array   $params URL parameters            OPTIONAL
     * @param string  $output URL output type           OPTIONAL
     *
     * @return string
     */
    public static function getProtoRelativeShopURL(
        $url = '',
        array $params = [],
        $output = null
    ) {
        if (!preg_match('/^https?:\/\//Ss', $url)) {
            if (!isset($output)) {
                $output = static::URL_OUTPUT_FULL;
            }
            $hostDetails = \Includes\Utils\ConfigParser::getOptions('host_details');
            $host        = $hostDetails[static::isHTTPS() ? 'https_host' : 'http_host'];
            if ($host) {
                if (strpos($url, '/') !== 0) {
                    $url = $hostDetails['web_dir_wo_slash'] . '/' . $url;
                }

                foreach ($params as $name => $value) {
                    $url .= (false !== strpos($url, '?') ? '&' : '?') . $name . '=' . $value;
                }

                if (static::URL_OUTPUT_FULL == $output) {
                    // We are using the protocol-relative URLs for resources
                    $url = '//' . $host . $url;
                }
            }
        }

        return $url;
    }

    /**
     * Check for secure connection
     *
     * @return boolean
     */
    public static function isHTTPS()
    {
        if (null === static::$isHTTPS) {
            static::$isHTTPS = (isset($_SERVER['HTTPS']) && ('on' === strtolower($_SERVER['HTTPS']) || '1' == $_SERVER['HTTPS']))
                || (isset($_SERVER['SERVER_PORT']) && '443' == $_SERVER['SERVER_PORT'])
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO']);
        }

        return static::$isHTTPS;
    }

    /**
     * Return current URI
     *
     * @return string
     */
    public static function getSelfURI()
    {
        return isset($_SERVER['REQUEST_URI']) ? urldecode($_SERVER['REQUEST_URI']) : null;
    }

    /**
     * Return current URL
     *
     * @return string
     */
    public static function getCurrentURL()
    {
        return 'http' . (static::isHTTPS() ? 's' : '') . '://' . $_SERVER['HTTP_HOST']
        . (static::getSelfURI() ?: '');
    }

    /**
     * Return current shop URL
     *
     * @return string
     */
    public static function getCurrentShopURL()
    {
        $host = 'http' . (static::isHTTPS() ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
        $webdir = static::getWebdir() ? '/' . trim(static::getWebdir(), '/') : '';
        return $host . $webdir;
    }

    /**
     * Returns webdir.
     *
     * @return string
     */
    public static function getWebdir()
    {
        $hostDetails = static::getOptions('host_details');
        return $hostDetails['web_dir'];
    }

    /**
     * Check if provided string is a valid host part of URL
     *
     * @param string $str Host string
     *
     * @return boolean
     */
    public static function isValidURLHost($str)
    {
        $urlData = parse_url('http://' . $str . '/path');
        $host = $urlData['host'] . (isset($urlData['port']) ? ':' . $urlData['port'] : '');

        return ($host == $str);
    }

    /**
     * Get list of available shop domains
     *
     * @return array
     */
    public static function getShopDomains()
    {
        $result = [];

        $hostDetails = \Includes\Utils\ConfigParser::getOptions(['host_details']);
        $result[] = !empty($hostDetails['http_host_orig']) ? $hostDetails['http_host_orig'] : $hostDetails['http_host'];
        $result[] = !empty($hostDetails['https_host_orig']) ? $hostDetails['https_host_orig'] : $hostDetails['https_host'];

        if (!empty($hostDetails['admin_host'])) {
            $result[] = $hostDetails['admin_host'];
        }

        $domains = explode(',', $hostDetails['domains']);

        if (!empty($domains) && is_array($domains)) {
            foreach ($domains as $domain) {
                $result[] = $domain;
            }
        }

        return array_unique($result);
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
        return \Includes\Utils\ConfigParser::getOptions($option);
    }

    /**
     * @param string $url
     * @param string $param URL parameter to be deleted from URL
     *
     * @return string
     */
    public static function getUrlWithoutParam($url, $param)
    {
        $urlParts = parse_url($url);
        parse_str($urlParts['query'] ?? '', $queryParams);

        if (array_key_exists($param, $queryParams)) {
            unset($queryParams[$param]);
        }

        $queryString = http_build_query($queryParams);

        return (strlen($queryString) > 0) ? $urlParts['path'] . '?' . $queryString : $urlParts['path'];
    }

    /**
     * @param string $url
     * @param string $controllerTarget
     * @param string $affiliateId
     * @param string $installationLng
     * @param bool   $useInstallationLng OPTIONAL
     *
     * @return string
     */
    public static function getAffiliatedXCartURL(
        $url,
        $controllerTarget,
        $affiliateId,
        $installationLng,
        $useInstallationLng = true
    ) {
        if (empty($url)) {
            $url = 'https://www.x-cart.com/';
        }

        $params = [];

        if ($useInstallationLng && $installationLng) {
            $params[] = "sl={$installationLng}";
        }

        if ($controllerTarget) {
            $params[] = 'utm_source=XC5admin';
            $params[] = "utm_medium={$controllerTarget}";
            $params[] = 'utm_campaign=XC5admin';
        }

        if ($params) {
            $url .= (strpos($url, '?') ? '&' : '?') . implode('&', $params);
        }

        return $affiliateId
            ? 'https://www.x-cart.com/aff/?aff_id=' . $affiliateId . '&amp;url=' . urlencode($url)
            : $url;
    }

    /**
     * Get URL of the page where license can be purchased
     *
     * @param string $shopUrl
     * @param string $controllerTarget
     * @param string $affiliateId
     * @param string $installationLng
     * @param string $adminEmail
     * @param int    $id       OPTIONAL
     * @param array  $params   OPTIONAL
     * @param bool   $ignoreId OPTIONAL
     *
     * @return string
     */
    public static function getPurchaseURL(
        $shopUrl,
        $controllerTarget,
        $affiliateId,
        $installationLng,
        $adminEmail,
        $id = 0,
        array $params = [],
        $ignoreId = false
    ) {
        $commonParams = [
            'target'    => 'cart',
            'action'    => 'add',
            'store_url' => $shopUrl,
        ];

        if (!$ignoreId) {
            $params['xbid'] = (int) $id !== 0
                ? $id
                : 391; // XC Business Edition xbid = 391
        }

        if ($adminEmail) {
            $commonParams['email'] = $adminEmail;
        }

        $httpQuery = static::buildParamsHttpQuery(
            array_merge($commonParams, $params)
        );

        return static::getAffiliatedXCartURL(
            "https://market.x-cart.com/?{$httpQuery}",
            $controllerTarget,
            $affiliateId,
            $installationLng
        );
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public static function buildParamsHttpQuery(array $params)
    {
        $urlParams = [];

        foreach ($params as $k => $v) {
            $urlParams[] = $k . '=' . urlencode($v);
        }

        return implode('&', $urlParams);
    }
}
