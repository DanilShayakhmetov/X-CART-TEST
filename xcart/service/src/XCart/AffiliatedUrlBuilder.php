<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart;

class AffiliatedUrlBuilder
{
    /**
     * @var string
     */
    protected $utmSource;

    /**
     * @var string
     */
    protected $utmCampaign;

    /**
     * @var string
     */
    private $controllerTarget;

    /**
     * @var string
     */
    private $installationLng;

    /**
     * @var string
     */
    private $affiliateId;

    /**
     * AffiliatedUrlBuilder constructor.
     *
     * @param string $controllerTarget
     * @param string $installationLng
     * @param string $affiliateId
     * @param string $utmSource
     * @param string $utmCampaign
     */
    public function __construct($controllerTarget, $installationLng, $affiliateId, $utmSource, $utmCampaign)
    {
        $this->controllerTarget = $controllerTarget;
        $this->installationLng  = $installationLng;
        $this->affiliateId      = $affiliateId;
        $this->utmSource        = $utmSource;
        $this->utmCampaign      = $utmCampaign;
    }

    /**
     * @param string $affiliateId
     * @param string $controllerTarget
     * @param string $installationLng
     *
     * @return static
     */
    public static function build($affiliateId, $controllerTarget, $installationLng)
    {
        return new static(
            $controllerTarget,
            $installationLng,
            $affiliateId,
            'XC5admin',
            'XC5admin'
        );
    }

    /**
     * Return affiliate URL
     *
     * @param string  $url                Url part to add OPTIONAL
     * @param boolean $useInstallationLng Use installation language or not OPTIONAL
     *
     * @return string
     */
    public function getXCartURL($url = '', $useInstallationLng = true)
    {
        $affiliateId = $this->affiliateId;

        if (empty($url)) {
            $url = 'https://www.x-cart.com/';
        }

        $params = [];

        if ($useInstallationLng
            && $this->installationLng
        ) {
            $params[] = 'sl=' . $this->installationLng;
        }

        if ($this->controllerTarget) {
            $params[] = 'utm_source=' . $this->utmSource;
            $params[] = 'utm_medium=' . $this->controllerTarget;
            $params[] = 'utm_campaign=' . $this->utmCampaign;
        }

        if ($params) {
            $url .= (strpos($url, '?') ? '&' : '?') . implode('&', $params);
        }

        return $affiliateId
            ? 'https://www.x-cart.com/aff/?aff_id=' . $affiliateId . '&amp;url=' . urlencode($url)
            : $url;
    }
}
