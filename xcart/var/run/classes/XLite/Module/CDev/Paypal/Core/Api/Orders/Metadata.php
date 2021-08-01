<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-metadata
 *
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\NameValuePair[] postback_data
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\NameValuePair[] supplementary_data
 */
class Metadata extends PayPalModel
{
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
     * @return Metadata
     */
    public function setPostbackData($postback_data)
    {
        $this->postback_data = $postback_data;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\NameValuePair $postback_data
     *
     * @return Metadata
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
     * @return Metadata
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
     * @return Metadata
     */
    public function setSupplementaryData($supplementary_data)
    {
        $this->supplementary_data = $supplementary_data;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\NameValuePair $supplementary_data
     *
     * @return Metadata
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
     * @return Metadata
     */
    public function removeSupplementaryData($supplementary_data)
    {
        return $this->setSupplementaryData(
            array_diff($this->getSupplementaryData(), [$supplementary_data])
        );
    }
}
