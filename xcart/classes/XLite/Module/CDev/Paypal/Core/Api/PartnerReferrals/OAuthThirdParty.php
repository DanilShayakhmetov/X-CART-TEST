<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-oauth_third_party
 *
 * @property string   partner_client_id
 * @property string   merchant_client_id
 * @property string[] scopes
 * @property string   access_token
 * @property string   refresh_token
 */
class OAuthThirdParty extends PayPalModel
{
    /**
     * @return string
     */
    public function getPartnerClientId()
    {
        return $this->partner_client_id;
    }

    /**
     * @param string $partner_client_id
     *
     * @return OAuthThirdParty
     */
    public function setPartnerClientId($partner_client_id)
    {
        $this->partner_client_id = $partner_client_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantClientId()
    {
        return $this->merchant_client_id;
    }

    /**
     * @param string $merchant_client_id
     *
     * @return OAuthThirdParty
     */
    public function setMerchantClientId($merchant_client_id)
    {
        $this->merchant_client_id = $merchant_client_id;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param string[] $scopes
     *
     * @return OAuthThirdParty
     */
    public function setScopes($scopes)
    {
        $this->scopes = $scopes;

        return $this;
    }

    /**
     * @param string $scope
     *
     * @return OAuthThirdParty
     */
    public function addScope($scope)
    {
        if (!$this->getScopes()) {

            return $this->setScopes([$scope]);
        }

        return $this->setScopes(
            array_merge($this->getScopes(), [$scope])
        );
    }

    /**
     * @param string $scope
     *
     * @return OAuthThirdParty
     */
    public function removeName($scope)
    {
        return $this->setScopes(
            array_diff($this->getScopes(), [$scope])
        );
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * @param string $access_token
     *
     * @return OAuthThirdParty
     */
    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;

        return $this;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refresh_token;
    }

    /**
     * @param string $refresh_token
     *
     * @return OAuthThirdParty
     */
    public function setRefreshToken($refresh_token)
    {
        $this->refresh_token = $refresh_token;

        return $this;
    }
}
