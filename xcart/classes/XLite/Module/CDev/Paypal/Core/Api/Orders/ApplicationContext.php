<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-application_context
 *
 * @property string                                                    brand_name
 * @property string                                                    locale
 * @property string                                                    landing_page
 * @property string                                                    shipping_preference
 * @property string                                                    user_action
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\NameValuePair[] postback_data
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\NameValuePair[] supplementary_data
 */
class ApplicationContext extends PayPalModel
{
    /**
     * @return string
     */
    public function getBrandName()
    {
        return $this->brand_name;
    }

    /**
     * @param string $brand_name
     *
     * @return ApplicationContext
     */
    public function setBrandName($brand_name)
    {
        $this->brand_name = $brand_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return ApplicationContext
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getLandingPage()
    {
        return $this->landing_page;
    }

    /**
     * @param string $landing_page
     *
     * @return ApplicationContext
     */
    public function setLandingPage($landing_page)
    {
        $this->landing_page = $landing_page;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingPreference()
    {
        return $this->shipping_preference;
    }

    /**
     * Valid Values: ["NO_SHIPPING", "GET_FROM_FILE", "SET_PROVIDED_ADDRESS"]
     *
     * @param string $shipping_preference
     *
     * @return ApplicationContext
     */
    public function setShippingPreference($shipping_preference)
    {
        $this->shipping_preference = $shipping_preference;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAction()
    {
        return $this->user_action;
    }

    /**
     * Valid Values: ["commit"]
     *
     * @param string $user_action
     *
     * @return ApplicationContext
     */
    public function setUserAction($user_action)
    {
        $this->user_action = $user_action;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\NameValuePair[]
     */
    public function getPostbackData()
    {
        return $this->postback_data;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\NameValuePair[] $postback_data
     *
     * @return ApplicationContext
     */
    public function setPostbackData($postback_data)
    {
        $this->postback_data = $postback_data;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\NameValuePair $postback_data
     *
     * @return ApplicationContext
     */
    public function addPostbackData($postback_data)
    {
        if (!$this->getPostbackData()) {

            return $this->setPostbackData([$postback_data]);
        }

        return $this->setPostbackData(
            array_merge($this->getPostbackData(), [$postback_data])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\NameValuePair $postback_data
     *
     * @return ApplicationContext
     */
    public function removePostbackData($postback_data)
    {
        return $this->setPostbackData(
            array_diff($this->getPostbackData(), [$postback_data])
        );
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\NameValuePair[]
     */
    public function getSupplementaryData()
    {
        return $this->supplementary_data;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\NameValuePair[] $supplementary_data
     *
     * @return ApplicationContext
     */
    public function setSupplementaryData($supplementary_data)
    {
        $this->supplementary_data = $supplementary_data;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\NameValuePair $supplementary_data
     *
     * @return ApplicationContext
     */
    public function addSupplementaryData($supplementary_data)
    {
        if (!$this->getSupplementaryData()) {

            return $this->setSupplementaryData([$supplementary_data]);
        }

        return $this->setSupplementaryData(
            array_merge($this->getSupplementaryData(), [$supplementary_data])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\NameValuePair $supplementary_data
     *
     * @return ApplicationContext
     */
    public function removeSupplementaryData($supplementary_data)
    {
        return $this->setSupplementaryData(
            array_diff($this->getSupplementaryData(), [$supplementary_data])
        );
    }
}
