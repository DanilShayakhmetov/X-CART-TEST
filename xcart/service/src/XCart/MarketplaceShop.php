<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart;

class MarketplaceShop
{
    /**
     * @var string
     */
    private $xbEndpoint;

    /**
     * @var string
     */
    private $shopUrl;

    /**
     * @var string
     */
    private $adminLogin;

    /**
     * @var string
     */
    private $licenseKeyMd5;

    /**
     * @var AffiliatedUrlBuilder
     */
    private $affiliatedUrlBuilder;

    /**
     * MarketplaceShop constructor.
     *
     * @param string               $xbHost
     * @param string               $shopUrl
     * @param string               $adminLogin
     * @param string               $licenseKeyMd5
     * @param AffiliatedUrlBuilder $affiliatedUrlBuilder
     */
    public function __construct(
        $xbHost,
        $shopUrl,
        $adminLogin,
        $licenseKeyMd5,
        AffiliatedUrlBuilder $affiliatedUrlBuilder
    ) {
        $this->xbEndpoint           = "{$xbHost}/customer.php";
        $this->shopUrl              = $shopUrl;
        $this->adminLogin           = $adminLogin;
        $this->licenseKeyMd5        = $licenseKeyMd5;
        $this->affiliatedUrlBuilder = $affiliatedUrlBuilder;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param string $shopUrl
     * @param string $adminLogin
     * @param string $licenseKeyMd5
     * @param string $affiliateId
     * @param string $controllerTarget
     * @param string $installationLng
     * @param string $xbHost
     *
     * @return static
     */
    public static function build(
        $shopUrl,
        $adminLogin,
        $licenseKeyMd5,
        $affiliateId,
        $controllerTarget,
        $installationLng,
        $xbHost = null
    ) {
        return new static(
            $xbHost ?? \Includes\Utils\ConfigParser::getOptions(['marketplace', 'xb_host']) ?? 'secure.x-cart.com',
            $shopUrl,
            $adminLogin,
            $licenseKeyMd5,
            AffiliatedUrlBuilder::build($affiliateId, $controllerTarget, $installationLng)
        );
    }

    /**
     * URL of the page where license can be purchased
     *
     * @param integer $id     Product ID                OPTIONAL
     * @param array   $params Additional URL parameters OPTIONAL
     * @param bool    $ignoreId
     *
     * @return string
     */
    public function getPurchaseURL($id = 0, array $params = [], $ignoreId = false)
    {
        return (new AffiliatedUrlBuilder(
            'activate_key',
            'en',
            null,
            'XC5admin',
            'XC5admin'
        ))->getXCartURL('https://www.x-cart.com/contact-us.html');
    }

    /**
     * @param string $xcProductId
     * @param string $returnUrl
     * @param array  $params
     * @param bool   $ignoreId
     *
     * @return string
     */
    public function getBuyNowURL($xcProductId, $returnUrl, array $params = [], $ignoreId = false)
    {
        $commonParams = [
            'target'           => 'generate_invoice',
            'action'           => 'buy',
            'proxy_checkout'   => 1,
            'inapp_return_url' => $returnUrl,
        ];

        if ($this->licenseKeyMd5) {
            $commonParams['lickey_1'] = $this->licenseKeyMd5;
        }

        $params    = $this->addIdToParamsIfNeeded('add_1', $xcProductId, $params, $ignoreId);
        $params    = $this->prepareParams($params, $commonParams);
        $httpQuery = $this->buildParamsHttpQuery($params);

        return "https://{$this->xbEndpoint}?{$httpQuery}";
    }

    /**
     * @param string $prolongationId
     * @param string $license
     * @param string $returnUrl
     * @param array  $params
     * @param bool   $ignoreId
     *
     * @return string
     */
    public function getRenewalURL($prolongationId, $license, $returnUrl, array $params = [], $ignoreId = false)
    {
        $commonParams = [
            'target'           => 'generate_invoice',
            'action'           => 'buy',
            'proxy_checkout'   => 1,
            'inapp_return_url' => $returnUrl,
            'add_1'            => $prolongationId,
            'lickey_1'         => md5($license),
        ];

        $params    = $this->prepareParams($params, $commonParams);
        $httpQuery = $this->buildParamsHttpQuery($params);

        return "https://{$this->xbEndpoint}?{$httpQuery}";
    }

    /**
     * @param string $keys
     * @param string $returnUrl
     * @param array  $params
     * @param bool   $ignoreId
     *
     * @return string
     */
    public function getRenewalAllURL($keys, $returnUrl, array $params = [], $ignoreId = false)
    {
        $commonParams = [
            'target'           => 'generate_invoice',
            'action'           => 'buy',
            'proxy_checkout'   => 1,
            'inapp_return_url' => $returnUrl
        ];

        foreach ($keys as $index => $key) {
            $commonParams['add_' . ($index + 1)] = $key['prolongKey'];
            $commonParams['lickey_' . ($index + 1)] = $key['keyValue'];
        }

        $params    = $this->prepareParams($params, $commonParams);
        $httpQuery = $this->buildParamsHttpQuery($params);

        return "https://{$this->xbEndpoint}?{$httpQuery}";
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param string $name
     * @param int    $id
     * @param array  $params
     * @param bool   $ignoreId
     *
     * @return array
     */
    protected function addIdToParamsIfNeeded($name, $id = 0, array $params = [], $ignoreId = false)
    {
        if (!$ignoreId) {
            $params[$name] = (int) $id !== 0
                ? $id
                : 391; // XC Business Edition xbid = 391
        }

        return $params;
    }

    /**
     * @param array $params
     * @param array $commonParams
     *
     * @return array
     */
    protected function prepareParams(array $params, array $commonParams)
    {
        if ($this->adminLogin) {
            $commonParams['email'] = $this->adminLogin;
        }

        return array_merge($commonParams, $params);
    }

    /**
     * @param array $params
     *
     * @return string
     */
    protected function buildParamsHttpQuery(array $params)
    {
        $urlParams = [];

        foreach ($params as $k => $v) {
            $urlParams[] = $k . '=' . urlencode($v);
        }

        return implode('&', $urlParams);
    }
}
