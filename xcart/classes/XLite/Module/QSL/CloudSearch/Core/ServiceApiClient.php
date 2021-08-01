<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use Includes\Utils\ConfigParser;
use Includes\Utils\URLManager;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\HTTP\Request;
use XLite\Core\Router;
use XLite\Logger;
use XLite\Module\QSL\CloudSearch\Main;


/**
 * CloudSearch API client
 */
class ServiceApiClient
{
    /**
     * CloudSearch service access details
     */
    const CLOUD_SEARCH_URL = 'https://cloudsearch.x-cart.com';
    const CLOUD_SEARCH_REQUEST_SECRET_KEY_URL = '/api/v1/getkey';
    const CLOUD_SEARCH_REMOTE_IFRAME_URL = '/api/v1/iframe?key=';
    const CLOUD_SEARCH_REGISTER_URL = '/api/v1/register';
    const CLOUD_SEARCH_SEARCH_URL = '/api/v1/search';
    const CLOUD_SEARCH_PLAN_INFO_URL = '/api/v1/plan-info';
    const CLOUD_SEARCH_WEBHOOK_URL = '/api/v1/webhook';

    const SEARCH_REQUEST_TIMEOUT = 5;
    const PLAN_INFO_REQUEST_TIMEOUT = 3;

    const WEBHOOK_TIMEOUT = 3;
    const WRITE_TRIES = 3;

    protected static $resultsCache = [];

    /**
     * Request CloudSearch registration
     *
     * @return void
     */
    public function register()
    {
        $requestUrl = $this->getCloudSearchUrl() . static::CLOUD_SEARCH_REGISTER_URL;

        $shopUrl = $this->getShopUrl();

        $shopKey = md5(uniqid(rand(), true));

        Database::getRepo('XLite\Model\TmpVar')->setVar('cloud_search_shop_key', $shopKey);

        $request       = new Request($requestUrl);
        $request->body = [
            'shopUrl'  => $shopUrl,
            'shopKey'  => $shopKey,
            'shopType' => 'xc5',
        ];

        $response = $request->sendRequest();

        if ($response && $response->code == 200) {
            $data = json_decode($response->body, true);

            if ($data && !empty($data['apiKey'])) {
                $this->storeApiKey($data['apiKey']);

                Config::updateInstance();
            }

            Database::getRepo('XLite\Model\TmpVar')->removeVar('cloud_search_shop_key');
        }
    }

    /**
     * Search functionality on the product list
     *
     * @param SearchParametersInterface $params
     *
     * @return array
     */
    public function search(SearchParametersInterface $params)
    {
        $params = $params->getParameters();

        $paramsHash = md5(serialize($params));

        if (!array_key_exists($paramsHash, self::$resultsCache)) {
            $response = $this->performSearchRequest($params);

            self::$resultsCache[$paramsHash] = $response && $response->code == 200
                ? $this->extractSearchResultsFromResponse($response)
                : null;
        }

        return self::$resultsCache[$paramsHash];
    }

    /**
     * Get CloudSearch service URL that defaults to https://cloudsearch.x-cart.com but can be overridden
     * with CLOUD_SEARCH_URL env var
     *
     * @return string
     */
    protected function getCloudSearchUrl()
    {
        return !empty($_SERVER['CLOUD_SEARCH_URL']) ? $_SERVER['CLOUD_SEARCH_URL'] : static::CLOUD_SEARCH_URL;
    }

    /**
     * Get search api endpoint url
     *
     * @return string
     */
    public function getSearchApiUrl()
    {
        return $this->getCloudSearchUrl() . static::CLOUD_SEARCH_SEARCH_URL;
    }

    /**
     * Get CloudSearch API key
     *
     * @return mixed
     */
    public function getApiKey()
    {
        return Config::getInstance()->QSL->CloudSearch->api_key;
    }

    /**
     * Get CloudSearch API key
     *
     * @return mixed
     */
    public function getSecretKey()
    {
        return Config::getInstance()->QSL->CloudSearch->secret_key;
    }

    /**
     * Retrieve search results from the response body
     *
     * @param \PEAR2\HTTP\Request\Response $response Response object
     *
     * @return array
     */
    protected function extractSearchResultsFromResponse($response)
    {
        $input = json_decode($response->body, true);

        $products = $input
        && $input['products']
        && count($input['products']) > 0 ? $input['products'] : [];

        return [
            'products'         => $products,
            'numFoundProducts' => $input['numFoundProducts'],
            'facets'           => $input['facets'],
            'stats'            => $input['stats'],
        ];
    }

    /**
     * Perform product search request (ALL) into the CloudSearch service
     *
     * @param array $params
     *
     * @return \PEAR2\HTTP\Request\Response
     */
    protected function performSearchRequest(array $params)
    {
        $request = new Request($this->getSearchApiUrl());

        $request->setAdditionalOption(\CURLOPT_TIMEOUT, self::SEARCH_REQUEST_TIMEOUT);

        $data = [
                'apiKey' => $this->getApiKey(),
                'all'    => 1,
            ] + $params;

        $request->body = json_encode($data);
        $request->verb = 'POST';
        $request->setHeader('Content-Type', 'application/json');

        return $request->sendRequest();
    }

    /**
     * Request CS plan info
     *
     * @return mixed|null
     */
    public function getPlanInfo()
    {
        $apiKey    = $this->getApiKey();
        $secretKey = $this->getSecretKey();

        $requestUrl = $this->getCloudSearchUrl() . static::CLOUD_SEARCH_PLAN_INFO_URL;

        $request = new Request($requestUrl);

        $request->setAdditionalOption(\CURLOPT_TIMEOUT, self::PLAN_INFO_REQUEST_TIMEOUT);

        $request->body = [
            'apiKey'    => $apiKey,
            'secretKey' => $secretKey,
        ];

        $response = $request->sendRequest();

        return $response && $response->code == 200 ? json_decode($response->body, true) : null;
    }

    /**
     * Get CloudSearch dashboard url
     *
     * @param $secretKey
     * @param $params
     *
     * @return string
     */
    public function getDashboardIframeUrl($secretKey, $params)
    {
        $features = ['cloud_filters', 'admin_search'];

        return $this->getCloudSearchUrl()
            . static::CLOUD_SEARCH_REMOTE_IFRAME_URL
            . $secretKey
            . '&' . http_build_query($params +
                [
                    'client_features' => $features,
                    'locale'          => \XLite\Core\Session::getInstance()->getLanguage()->getCode(),
                ]);
    }

    /**
     * Get store url without script part
     *
     * @return string
     */
    protected function getShopUrl()
    {
        if (Main::isXCCloud() && Main::getXCCloudHost()) {
            return 'https://' . Main::getXCCloudHost();
        }

        $router = Router::getInstance();

        if (method_exists($router, 'disableLanguageUrlsTmp')) {
            $router->disableLanguageUrlsTmp();
        }

        $url = preg_replace(
            '/[^\/]*.php$/',
            '',
            URLManager::getShopURL(Converter::buildURL())
        );

        if (method_exists($router, 'releaseLanguageUrlsTmp')) {
            $router->releaseLanguageUrlsTmp();
        }

        $protocol = URLManager::isHTTPS() ? 'https' : 'http';

        $hostDetails = ConfigParser::getOptions('host_details');

        if (Main::isMultiDomain() && isset($hostDetails[$protocol . '_host_orig'])) {
            $original_host = $hostDetails[$protocol . '_host_orig'];

            $scheme = parse_url($url, PHP_URL_SCHEME);
            $host   = parse_url($url, PHP_URL_HOST);

            $url = $scheme . '://' . $original_host
                . substr($url, strlen($scheme) + strlen('://') + strlen($host));
        }

        return $url;
    }

    /**
     * Store API key in the DB
     *
     * @param $key
     *
     * @return void
     */
    protected function storeApiKey($key)
    {
        $repo = Database::getRepo('XLite\Model\Config');

        $apiKeySetting = $repo->findOneBy([
            'name'     => 'api_key',
            'category' => 'QSL\CloudSearch',
        ]);

        $apiKeySetting->setValue($key);

        Database::getEM()->flush();
    }

    public function sendWebhookEvent($eventData)
    {
        $url = $this->getCloudSearchUrl();

        $parts = parse_url($url);

        $parts['port'] = (isset($parts['schema']) && $parts['schema'] === 'https') ? 443 : 80;

        $remote = 'tcp://' . $parts['host'] . ':' . $parts['port'];

        $socket = @stream_socket_client(
            $remote,
            $errno,
            $errstr,
            static::WEBHOOK_TIMEOUT
        );

        if (false === $socket) {
            Logger::logCustom('CloudSearchLogs', "Error: $errno - $errstr");

            return;
        }

        stream_set_timeout($socket, static::WEBHOOK_TIMEOUT);

        $request = $this->buildHttpRequest($eventData);

        $toBeSentData = strlen($request);

        $triesLeft = static::WRITE_TRIES;

        for ($sentData = 0; $sentData < $toBeSentData; $sentData += $sent) {
            $sent = fwrite($socket, substr($request, $sentData));

            if ($sent === false || $sent === 0) {
                $triesLeft--;

                if (!$triesLeft) {
                    break;
                } else {
                    sleep(1);
                }
            }
        }

        fclose($socket);
    }

    protected function buildHttpRequest($data) {
        $data = json_encode($data);

        $request = 'POST ' . $this->getCloudSearchUrl() . static::CLOUD_SEARCH_WEBHOOK_URL . ' HTTP/1.1' . "\r\n";
        $request .= 'Host: ' . parse_url(static::getCloudSearchUrl())['host'] . "\r\n";
        $request .= 'Content-Type: application/json; charset=UTF-8' . "\r\n";
        $request .= 'Content-Length: ' . strlen($data) . "\r\n";
        $request .= 'Cache-Control: no-cache' . "\r\n";
        $request .= 'X-Api-Key: ' . $this->getSecretKey() . "\r\n";
        $request .= "\r\n";
        $request .= $data;

        return $request;
    }
}
