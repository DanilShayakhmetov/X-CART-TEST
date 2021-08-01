<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalResourceModel;
use PayPal\Transport\PayPalRestCall;
use PayPal\Rest\ApiContext;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-referral_data
 *
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\User                    customer_data
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Capability[]            requested_capabilities
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\WebExperiencePreference web_experience_preference
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\LegalConsent[]          collected_consents
 * @property string[]                                                                    products
 */
class ReferralData extends PayPalResourceModel
{
    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\User
     */
    public function getCustomerData()
    {
        return $this->customer_data;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\User $customer_data
     *
     * @return ReferralData
     */
    public function setCustomerData($customer_data)
    {
        $this->customer_data = $customer_data;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Capability[]
     */
    public function getRequestedCapabilities()
    {
        return $this->requested_capabilities;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Capability[] $requested_capabilities
     *
     * @return ReferralData
     */
    public function setRequestedCapabilities($requested_capabilities)
    {
        $this->requested_capabilities = $requested_capabilities;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Capability $requested_capability
     *
     * @return ReferralData
     */
    public function addRequestedCapability($requested_capability)
    {
        if (!$this->getRequestedCapabilities()) {

            return $this->setRequestedCapabilities([$requested_capability]);
        }

        return $this->setRequestedCapabilities(
            array_merge($this->getRequestedCapabilities(), [$requested_capability])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Capability $requested_capability
     *
     * @return ReferralData
     */
    public function removeRequestedCapability($requested_capability)
    {
        return $this->setRequestedCapabilities(
            array_diff($this->getRequestedCapabilities(), [$requested_capability])
        );
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\WebExperiencePreference
     */
    public function getWebExperiencePreference()
    {
        return $this->web_experience_preference;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\WebExperiencePreference $web_experience_preference
     *
     * @return ReferralData
     */
    public function setWebExperiencePreference($web_experience_preference)
    {
        $this->web_experience_preference = $web_experience_preference;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\LegalConsent[]
     */
    public function getCollectedConsents()
    {
        return $this->collected_consents;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\LegalConsent[] $collected_consents
     *
     * @return ReferralData
     */
    public function setCollectedConsents($collected_consents)
    {
        $this->collected_consents = $collected_consents;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\LegalConsent $collected_consent
     *
     * @return ReferralData
     */
    public function addCollectedConsent($collected_consent)
    {
        if (!$this->getCollectedConsents()) {

            return $this->setCollectedConsents([$collected_consent]);
        }

        return $this->setCollectedConsents(
            array_merge($this->getCollectedConsents(), [$collected_consent])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\LegalConsent $collected_consent
     *
     * @return ReferralData
     */
    public function removeCollectedConsent($collected_consent)
    {
        return $this->setCollectedConsents(
            array_diff($this->getCollectedConsents(), [$collected_consent])
        );
    }

    /**
     * @return string[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Valid Values: ["EXPRESS_CHECKOUT", "PPPLUS", "WP_PRO"]
     *
     * @param string[] $products
     *
     * @return ReferralData
     */
    public function setProducts($products)
    {
        $this->products = $products;

        return $this;
    }

    /**
     * @param string $product
     *
     * @return ReferralData
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
     * @param string $product
     *
     * @return ReferralData
     */
    public function removeProduct($product)
    {
        return $this->setProducts(
            array_diff($this->getProducts(), [$product])
        );
    }

    /**
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return ReferralData
     */
    public function create($apiContext = null, $restCall = null)
    {
        $payLoad = $this->toJSON();

        $json = self::executeCall(
            '/v1/customer/partner-referrals',
            'POST',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        return $this->fromJson($json);
    }

    /**
     * @param string         $partnerReferralId
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return ReferralData
     */
    public static function get($partnerReferralId, $apiContext = null, $restCall = null)
    {
        $payLoad = '';

        $json = self::executeCall(
            '/v1/customer/partner-referrals/' . $partnerReferralId,
            'GET',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        return (new self)->fromJson($json);
    }
}
