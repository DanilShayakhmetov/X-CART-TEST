<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalResourceModel;
use PayPal\Rest\ApiContext;
use PayPal\Transport\PayPalRestCall;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-merchant_integration
 *
 * @property string                                                                 tracking_id
 * @property string                                                                 merchant_id
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Product[]          products
 * @property bool                                                                   payments_receivable
 * @property bool                                                                   primary_email_confirmed
 * @property string                                                                 primary_email
 * @property string                                                                 date_created
 * @property string[]                                                               granted_permissions
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Credential         api_credentials
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\OAuthIntegration[] oauth_integrations
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Limitation[]       limitations
 */
class MerchantIntegration extends PayPalResourceModel
{
    /**
     * @return string
     */
    public function getTrackingId()
    {
        return $this->tracking_id;
    }

    /**
     * @param string $tracking_id
     *
     * @return MerchantIntegration
     */
    public function setTrackingId($tracking_id)
    {
        $this->tracking_id = $tracking_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchant_id;
    }

    /**
     * @param string $merchant_id
     *
     * @return MerchantIntegration
     */
    public function setMerchantId($merchant_id)
    {
        $this->merchant_id = $merchant_id;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Product[] $products
     *
     * @return MerchantIntegration
     */
    public function setProducts($products)
    {
        $this->products = $products;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Product $product
     *
     * @return MerchantIntegration
     */
    public function addProduct($product)
    {
        if (!$this->getProducts()) {

            return $this->setProducts([$product]);
        }

        return $this->setProducts(
            array_merge($this->getProducts(), [$product])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Product $product
     *
     * @return MerchantIntegration
     */
    public function removeProduct($product)
    {
        return $this->setProducts(
            array_diff($this->getProducts(), [$product])
        );
    }

    /**
     * @return bool
     */
    public function isPaymentsReceivable()
    {
        return $this->payments_receivable;
    }

    /**
     * @param bool $payments_receivable
     *
     * @return MerchantIntegration
     */
    public function setPaymentsReceivable($payments_receivable)
    {
        $this->payments_receivable = $payments_receivable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPrimaryEmailConfirmed()
    {
        return $this->primary_email_confirmed;
    }

    /**
     * @param bool $primary_email_confirmed
     *
     * @return MerchantIntegration
     */
    public function setPrimaryEmailConfirmed($primary_email_confirmed)
    {
        $this->primary_email_confirmed = $primary_email_confirmed;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrimaryEmail()
    {
        return $this->primary_email;
    }

    /**
     * @param string $primary_email
     *
     * @return MerchantIntegration
     */
    public function setPrimaryEmail($primary_email)
    {
        $this->primary_email = $primary_email;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * @param string $date_created
     *
     * @return MerchantIntegration
     */
    public function setDateCreated($date_created)
    {
        $this->date_created = $date_created;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getGrantedPermissions()
    {
        return $this->granted_permissions;
    }

    /**
     * @param string[] $granted_permissions
     *
     * @return MerchantIntegration
     */
    public function setGrantedPermissions($granted_permissions)
    {
        $this->granted_permissions = $granted_permissions;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Credential
     */
    public function getApiCredentials()
    {
        return $this->api_credentials;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Credential $api_credentials
     *
     * @return MerchantIntegration
     */
    public function setApiCredentials($api_credentials)
    {
        $this->api_credentials = $api_credentials;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\OAuthIntegration[]
     */
    public function getOauthIntegrations()
    {
        return $this->oauth_integrations;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\OAuthIntegration[] $oauth_integrations
     *
     * @return MerchantIntegration
     */
    public function setOauthIntegrations($oauth_integrations)
    {
        $this->oauth_integrations = $oauth_integrations;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\OAuthIntegration $oauth_integration
     *
     * @return MerchantIntegration
     */
    public function addOauthIntegration($oauth_integration)
    {
        if (!$this->getOauthIntegrations()) {

            return $this->setOauthIntegrations([$oauth_integration]);
        }

        return $this->setOauthIntegrations(
            array_merge($this->getOauthIntegrations(), [$oauth_integration])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\OAuthIntegration $oauth_integration
     *
     * @return MerchantIntegration
     */
    public function removeOauthIntegration($oauth_integration)
    {
        return $this->setOauthIntegrations(
            array_diff($this->getOauthIntegrations(), [$oauth_integration])
        );
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Limitation[]
     */
    public function getLimitations()
    {
        return $this->limitations;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Limitation[] $limitations
     *
     * @return MerchantIntegration
     */
    public function setLimitations($limitations)
    {
        $this->limitations = $limitations;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Limitation $limitation
     *
     * @return MerchantIntegration
     */
    public function addLimitation($limitation)
    {
        if (!$this->getLimitations()) {

            return $this->setLimitations([$limitation]);
        }

        return $this->setLimitations(
            array_merge($this->getLimitations(), [$limitation])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Limitation $limitation
     *
     * @return MerchantIntegration
     */
    public function removeLimitation($limitation)
    {
        return $this->setLimitations(
            array_diff($this->getLimitations(), [$limitation])
        );
    }

    /**
     * @param string         $partnerId
     * @param string         $merchantId
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return MerchantIntegration
     */
    public static function get($partnerId, $merchantId, $apiContext = null, $restCall = null)
    {
        $payLoad = '';

        $json = self::executeCall(
            '/v1/customer/partners/' . $partnerId . '/merchant-integrations/' . $merchantId,
            'GET',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        return (new self)->fromJson($json);
    }
}
